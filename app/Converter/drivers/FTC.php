<?php

namespace App\Converter\Drivers;

use Exception;
use PHPExcel_Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\FTC\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;
use App\Converter\Exceptions\FormattingException;

class FTC extends BaseDriver implements Driver
{
    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Net Comm',
        'Comm Sale Amt'
    ];

    /**
     * A mandatory field to be matched in conversions.
     *
     * @var array
     */
    public static $mandatoryMatch = [
        'state'
    ];

    /**
     * The input tabs.
     *
     * @var array
     */
    protected $tabs = [
        [
            'name' => 'F77',
            'conditioners' => [
                'customer_name',
                'document_date',
                'city_from_sop_doc',
                'state_from_sop_doc'
            ]
        ],
        [
            'name' => 'GraingerPOS',
            'header_row' => 3,
            'conditioners' => [
                'month'
            ]
        ],
        [
            'name' => 'NorthernSafetyPOS',
            'header_row' => 2,
            'conditioners' => [
                'month'
            ]
        ]
    ];

    /**
     * Process the input file to extract the input rows.
     * @param string $path
     * @return self
     */
    public function input(string $path)
    {
        parent::input($path);

        foreach ($this->tabs as $tab) {
            Excel::selectSheets($tab['name'])->load($this->inputPath, function ($reader) use ($tab) {
                try {
                    if (in_array('header_row', array_keys($tab))) {
                        $reader->setHeaderRow($tab['header_row']);
                    }
                    $rows = $reader->get()->toArray();
                    foreach ($rows as $key => $row) {
                        $valid = true;
                        foreach ($tab['conditioners'] as $condition_key) {
                            if ($row[$condition_key] == null) {
                                $valid = false;
                                break;
                            }
                        }
                        if (!$valid) {
                            continue;
                        }
                        $row['line'] = $key;
                        $this->inputRows[$tab['name']][] = $row;
                    }
                } catch (PHPExcel_Exception $e) {
                    return;
                }
            });
        }

        return $this;
    }

    /**
     * Process the input file and convert it.
     *
     * @return self
     */
    public function process()
    {
        $f77dates = [];

        // Here we check if there was no rows entered, that way we should know
        // the uploaded file doesn't contain the tabs we're expecting.
        // So in that case we're throwing an error the the user.
        if ($this->inputRows == null) {
            throw new FormattingException(sprintf(
                'We cannot find the tabs! We expect %s, %s and %s.',
                $this->tabs[0]['name'],
                $this->tabs[1]['name'],
                $this->tabs[2]['name']
            ));
        }

        foreach ($this->inputRows['F77'] as $row) {
            try {
                // Parse the city and state and set a fallback
                // in case the column has `will call` string.
                if (str_contains($row['city_from_sop_doc'], 'WILL CALL') || str_contains($row['state_from_sop_doc'], 'WILL CALL')) {
                    $city = $row['city_from_sop_doc'] ?: null;
                    $state = $row['state_from_sop_doc'] ?: null;
                } else {
                    $city = $row['city_from_sop_doc'];
                    $state = $row['state_from_sop_doc'];
                }

                // Add the document date to array of dates
                // so we can compare between them later
                // to get the most recent one.
                if (!empty($row['document_date'])) {
                    $f77dates[] = $row['document_date'];
                }

                $translator = service('translator')->setDriver('FTC')
                                                ->setName($row['customer_name'])
                                                ->setCity($city)
                                                ->setState($state)
                                                ->setAddress($row['city_from_sop_doc'])
                                                ->process();

                $this->outputRows['SA Import'][] = (new Model)->documentDate($row['document_date'])
                                                        ->customerName($translator->output->name)
                                                        ->city($translator->output->city)
                                                        ->state($translator->output->state)
                                                        ->salesAmount($row['comm_sale_amt'])
                                                        ->commissionRate($row['comm_rate'])
                                                        ->netCommission($row['net_comm_amt'])
                                                        ->originalCity($city)
                                                        ->originalState($state)
                                                        ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line'], [
                    'tab' => 'F77'
                ]);
            }
        }

        // Get the most recent date in F77 tab
        // so we can use it in Grainger tab.
        $recentDate = $f77dates[count($f77dates) - 1];
        foreach ($f77dates as $date) {
            if ($date->greaterThan($recentDate)) {
                $recentDate = $date;
            }
        }

        if (in_array('GraingerPOS', array_keys($this->inputRows))) {
            foreach ($this->inputRows['GraingerPOS'] as $row) {
                // Try to parse the address components from the provided data.
                // If something went wrong, it means the address format is
                // not what we expected. In that case we call Geocoder
                // service to get the address components.
                try {
                    if (str_contains($row['plant_location'], 'WILL CALL')) {
                        $city = null;
                        $state = null;
                        $zip = null;
                    } else {
                        try {
                            $addressComponents = explode(',', $row['plant_location']);
                            $city = $addressComponents[0];
                            $state = $addressComponents[1];
                        } catch (Exception $e) {
                            $address = $this->extractAddress($row['plant_location'] . ' ' . $row['plant_postal_code']);
                            $zip = $address->zip;
                            $city = $address->city;
                            $state = $address->state;
                        }
                    }

                    $translator = service('translator')->setDriver('FTC')
                                                    ->setName('Grainger')
                                                    ->setCity($city)
                                                    ->setState($state)
                                                    ->setAddress($row['plant_location'] . ' ' . $row['plant_postal_code'])
                                                    ->process();
                    $this->outputRows['SA Import'][] = (new Model)->documentDate($recentDate->endOfMonth())
                                                                ->customerName($translator->output->name)
                                                                ->city($translator->output->city)
                                                                ->state($translator->output->state)
                                                                ->salesAmount($row['cost_of_product_sold'])
                                                                ->commissionRate(0.0405)
                                                                ->netCommission($row['cost_of_product_sold'] * (4.05 / 100))
                                                                ->originalCity($city)
                                                                ->originalState($state)
                                                                ->quantity($row['quantity_sold'])
                                                                ->toArray();
                } catch (Exception $e) {
                    throw new UsefulException($e, $row['line'], [
                        'tab' => 'Grainger'
                    ]);
                }
            }
        }

        // Parse the data from Northern Safety Tab. This is a kind of special case.
        // All rows should be combined into two rows only. Rows for NY
        // and rows for other states.
        if (in_array('NorthernSafetyPOS', array_keys($this->inputRows))) {
            $NYRow = [
                'Customer Name' => 'Northern Safety',
                'Comm Sale Amt' => 0,
                'Net Comm' => 0,
            ];

            $OtherRow = [
                'Customer Name' => 'Northern Safety',
                'Comm Sale Amt' => 0,
                'Net Comm' => 0,
            ];
            foreach ($this->inputRows['NorthernSafetyPOS'] as $row) {
                try {
                    $readCommissions = array_keys($row)[10];
                    $commission = floatval(preg_replace("/[^-0-9\.]/", '', $readCommissions));

                    $translator = service('translator')->setDriver('FTC')
                                                ->setName('Northern Safety Company')
                                                ->setCity($row['city'])
                                                ->setState($row['state'])
                                                ->process();

                    $this->outputRows['Northern Safety'][] = (new Model)->documentDate($row['month'])
                                                ->customerName($translator->output->name)
                                                ->city($translator->output->city)
                                                ->state($translator->output->state)
                                                ->salesAmount($row['extended_cost'])
                                                ->commissionRate($commission/100)
                                                ->netCommission((float) $row['extended_cost'] * (float) ($commission / 100))
                                                ->originalCity($row['city'])
                                                ->originalState($row['state'])
                                                ->toArray();
                    if (strtolower($translator->output->state) == 'ny') {
                        $NYRow['Comm Sale Amt'] += (float) $row['extended_cost'];
                        $NYRow['%age Used'] = $commission;
                        $NYRow['Net Comm'] += (float) $row['extended_cost'] * (float) ($commission / 100);
                    } else {
                        $OtherRow['Comm Sale Amt'] += (float) $row['extended_cost'];
                        $OtherRow['%age Used'] = $commission;
                        $OtherRow['Net Comm'] += (float) $row['extended_cost'] * (float) ($commission / 100);
                    }
                } catch (Exception $e) {
                    throw new UsefulException($e, $row['line'], [
                        'tab' => 'Northern Safety'
                    ]);
                }
            }

            // Adding the rows
            $this->outputRows['SA Import'][] = (new Model)->documentDate(null)
                    ->customerName($NYRow['Customer Name'])
                    ->city('Utica')
                    ->state('NY')
                    ->salesAmount($NYRow['Comm Sale Amt'])
                    ->commissionRate(isset($NYRow['%age Used']) ? $NYRow['%age Used'] : null)
                    ->netCommission($NYRow['Net Comm'])
                    ->toArray();
            $this->outputRows['SA Import'][] = (new Model)->documentDate(null)
                    ->customerName($OtherRow['Customer Name'])
                    ->city('Church Hill')
                    ->state('TN')
                    ->salesAmount($OtherRow['Comm Sale Amt'])
                    ->commissionRate(isset($OtherRow['%age Used']) ? $OtherRow['%age Used'] : null)
                    ->netCommission($OtherRow['Net Comm'])
                    ->toArray();
        }

        $this->outputRows['SA Import'] = service('sorter')
                                ->setRows($this->outputRows['SA Import'])
                                ->setCustomer('Customer Name')
                                ->setCity('SA City')
                                ->setState('SA State')
                                ->process();

        if (in_array('Northern Safety', array_keys($this->outputRows))) {
            $this->outputRows['Northern Safety'] = service('sorter')
                            ->setRows($this->outputRows['Northern Safety'])
                            ->setCustomer('Customer Name')
                            ->setCity('SA City')
                            ->setState('SA State')
                            ->process();
        }

        $outputRows = $this->outputRows;

        Excel::create($this->filename, function ($excel) use ($outputRows) {

            $excel->sheet('Main', function ($sheet) use ($outputRows) {
                $sheet->fromArray($outputRows['SA Import'], null, 'A1', true);
                $sheet->row(1, function ($row) {
                    $row->setFontFamily('Comic Sans MS');
                    $row->setFontWeight(true);
                    $row->setBackground('#0da128');
                });
            });

            if (in_array('Northern Safety', array_keys($outputRows))) {
                $excel->sheet('Northern Safety', function ($sheet) use ($outputRows) {
                    $sheet->fromArray($outputRows['Northern Safety'], null, 'A1', true);
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Comic Sans MS');
                        $row->setFontWeight(true);
                        $row->setBackground('#0da128');
                    });
                });
            }

            $this->setCurrencyColumns($excel, $outputRows['SA Import']);
        })->store('xls', storage_path('app/public/outputs'));

        $this->outputPath = 'storage/app/public/outputs/' . $this->filename . '.xls';

        return $this;
    }
}
