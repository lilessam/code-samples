<?php

namespace App\Converter\Drivers;

use Exception;
use Carbon\Carbon;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\MLK\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class MLK extends BaseDriver implements Driver
{
    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Sales Amount',
        'Comm'
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
     * A variable used to parse the data.
     *
     * @var array
     */
    private $lastRow = [
        'orderdate' => null,
        'inv_date' => null,
        'cust_id' => null,
        'inv_city' => null,
        'state' => null,
    ];

    /**
     * The date pulled from ReCap Tab (index => 3)
     *
     * @var string|null
     */
    private $recapMonth = null;

    /**
     * Process the input file to extract the input rows.
     * @param string $path
     * @return self
     */
    public function input(string $path)
    {
        parent::input($path);
        /**
         * Parse Original rows.
         */
        Excel::selectSheetsByIndex(0)->load($this->inputPath, function ($reader) {
            $reader->setHeaderRow(4);
            $rows = $reader->get()->toArray();
            try {
                foreach ($rows as $key => $row) {
                    if (!is_numeric($row['inv_no']) && $row['inv_no'] != null) {
                        $this->lastRow['cust_id'] = $row['inv_no'];
                    }

                    if ($row['shipstate'] != null) {
                        $this->lastRow['state'] = $row['shipstate'];
                    }

                    if ($row['orderdate'] != null) {
                        $this->lastRow['orderdate'] = $row['orderdate'];
                    }

                    if ($row['inv_date'] != null && $row['inv_no'] != null) {
                        $this->lastRow['inv_date'] = $row['inv_date'];
                        $this->lastRow['inv_no'] = $row['inv_no'];
                        continue;
                    }
                    if (($row['orderdate'] == null && $row['inv_date'] == null
                        && $row['inv_no'] == null && $row['item'] != null) || ($row['cust_no'] != null && $row['inv_no'] == null)) {
                            $row['orderdate'] = isset($this->lastRow['orderdate']) ? $this->lastRow['orderdate'] : null;
                            $row['inv_date'] = isset($this->lastRow['inv_date']) ? $this->lastRow['inv_date'] : null;
                            $row['inv_no'] = isset($this->lastRow['inv_no']) ? $this->lastRow['inv_no'] : null;
                    }

                    $row['cust_id'] = $this->lastRow['cust_id'];
                    $row['state'] = $this->lastRow['state'];

                    $row['line'] = $key;
                    $this->inputRows['original'][] = $row;
                }
            } catch (Exception $e) {
                throw new UsefulException($e, null, [
                    'tab' => 'Original'
                ]);
            }
        });

        /**
         * Parse Grainger Rows.
         */
        Excel::selectSheetsByIndex(1)->load($this->inputPath, function ($reader) {
            $reader->setHeaderRow(1);
            $rows = $reader->get()->toArray();
            try {
                foreach ($rows as $key => $row) {
                    if ($row['country'] != null) {
                        $row['line'] = $key;
                        $this->inputRows['grainger'][] = $row;
                    }
                }
            } catch (Exception $e) {
                throw new UsefulException($e, null, [
                    'tab' => 'Grainger'
                ]);
            }
        });

        /**
         * Parse R3 Rows.
         */
        Excel::selectSheetsByIndex(2)->load($this->inputPath, function ($reader) {
            $reader->setHeaderRow(4);
            $rows = $reader->get()->toArray();
            try {
                foreach ($rows as $key => $row) {
                    if ($row['customer_number'] != null) {
                        $row['line'] = $key;
                        $this->inputRows['r3'][] = $row;
                    }
                }
            } catch (Exception $e) {
                throw new UsefulException($e, null, [
                    'tab' => 'R3'
                ]);
            }
        });

        /**
         * Parse Recap Rows.
         */
        Excel::selectSheetsByIndex(3)->load($this->inputPath, function ($reader) {
            $rows = $reader->get()->toArray();
            try {
                foreach ($rows as $key => $row) {
                    if ($key == 1) {
                        $this->recapMonth = $row['_4'];
                    }
                    if ($row['_1'] == 'S&SACTI #9110 NJ' || $row['_1'] == 'S&SACTI NJ' || $row['_1'] == 'NABA NY') {
                        $this->inputRows['recap'][] = [
                            'customer' => $row['_1'],
                            'rate' => $row['_6']*100,
                            'sales' => $row['_7'],
                            'commission' => $row['_8'],
                            'state' => null,
                            'line' => $key
                        ];
                    }
                    if ($row[0] != null) {
                        $this->inputRows['recap'][] = [
                            'customer' => $row['_1'],
                            'rate' => 5,
                            'sales' => $row['_5'],
                            'commission' => 5/100 * (float) $row['_5'],
                            'state' => $row['_2'],
                            'line' => $key
                        ];
                    }
                }
            } catch (Exception $e) {
                throw new UsefulException($e, null, [
                    'tab' => 'Recap'
                ]);
            }
        });
        return $this;
    }

    /**
     * Process original sheet rows and put them in the output array.
     *
     * @return void
     */
    private function processOriginalRows()
    {
        foreach ($this->inputRows['original'] as $row) {
            try {
                if ($row['inv_date'] == null) {
                    continue;
                }
                $translator = service('translator')
                                ->setDriver('MLK')
                                ->setName($row['cust_id'])
                                ->setState($row['state'])
                                ->process();

                $this->outputRows[] = (new Model)->invoiceDate($row['inv_date'])
                    ->customerName($translator->output->name)
                    ->state($translator->output->state)
                    ->city($translator->output->city)
                    ->invoiceId($row['inv_no'])
                    ->item($row['item'])
                    ->quantitySold($row['qtysold'])
                    ->salesAmount($row['sales_amount'])
                    ->commissionRate(5)
                    ->commission(5/100 * (float) $row['sales_amount'])
                    ->orderDate($row['orderdate'])
                    ->originalState($row['state'])
                    ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line'], [
                    'tab' => 'Original'
                ]);
            }
        }
    }

    /**
     * Process Grainger rows and put them in output rows.
     *
     * @return void
     */
    private function processGraingerRows()
    {
        foreach ($this->inputRows['grainger'] as $row) {
            $address = $this->extractAddress($row['customer_zip_code_5_digits']);
            $city = $address->city;
            $state = $address->state;
            $translator = service('translator')
                                ->setDriver('MLK')
                                ->setName('Grainger')
                                ->setCity($city)
                                ->setState($state)
                                ->process();
            try {
                $model = new Model;
                $model->city($translator->output->city)
                    ->state($translator->output->state)
                    ->invoiceDate($this->recapMonth)
                    ->customerName($translator->output->name)
                    ->invoiceId('')
                    ->item($row['manuf_part_nbr'])
                    ->quantitySold($row['quantity_sold'])
                    ->salesAmount($row['cost_of_product_sold'])
                    ->commissionRate(3)
                    ->commission((float) $row['cost_of_product_sold'] / 100 * 3)
                    ->orderDate('')
                    ->originalCity($city)
                    ->originalState($state)
                    ->originalZipcode($row['customer_zip_code_5_digits']);

                $this->outputRows[] = $model->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line'], [
                    'tab' => 'Grainger'
                ]);
            }
        }
    }

    /**
     * Process R3 rows and put them in output rows.
     *
     * @return void
     */
    private function processR3Rows()
    {
        if (key_exists('r3', $this->inputRows) && count($this->inputRows['r3']) > 0) {
            $cost_key = collect(array_keys($this->inputRows['r3'][0]))->first(function ($o) { return str_contains($o, 'cost'); });
            $qty_key = collect(array_keys($this->inputRows['r3'][0]))->first(function ($o) { return str_contains($o, 'quantity'); });
        }

        foreach ($this->inputRows['r3'] as $row) {
            $translator = service('translator')
                                ->setDriver('MLK')
                                ->setName($row['vendor_name'])
                                ->setCity($row['city'])
                                ->setState($row['state'])
                                ->process();
            try {
                $model = new Model;
                $model->city($translator->output->city)
                    ->state($translator->output->state)
                    ->invoiceDate($this->recapMonth)
                    ->customerName($translator->output->name)
                    ->invoiceId('')
                    ->item($row['item_number'])
                    ->quantitySold($row[$qty_key])
                    ->salesAmount($row[$cost_key])
                    ->commissionRate(5)
                    ->commission((float) $row[$cost_key] / 100 * 5)
                    ->orderDate('')
                    ->originalCity($row['city'])
                    ->originalState($row['state']);
                $this->outputRows[] = $model->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line'], [
                    'tab' => 'R3'
                ]);
            }
        }
    }

    /**
     * Process Recap rows and put them in output rows.
     *
     * @return void
     */
    private function processRecapRows()
    {
        foreach ($this->inputRows['recap'] as $row) {
            $translator = service('translator')
                                ->setDriver('MLK')
                                ->setName($row['customer'])
                                ->setState($row['state'])
                                ->process();
            try {
                $model = new Model;
                $model->city($translator->output->city)
                    ->state($translator->output->state)
                    ->invoiceDate($this->recapMonth)
                    ->customerName($translator->output->name)
                    ->invoiceId('')
                    ->item('')
                    ->quantitySold('')
                    ->salesAmount((float) $row['sales'])
                    ->commissionRate($row['rate'])
                    ->commission((float) $row['commission'])
                    ->orderDate('')
                    ->originalState($row['state']);

                $this->outputRows[] = $model->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line'], [
                    'tab' => 'Recap'
                ]);
            }
        }
    }

    /**
     * Process the input file and convert it.
     *
     * @return self
     */
    public function process()
    {
        $this->processOriginalRows();
        $this->processGraingerRows();
        $this->processR3Rows();
        $this->processRecapRows();

        $this->outputRows = service('sorter')
                        ->setRows($this->outputRows)
                        ->setCustomer('Customer Name')
                        ->setState('SA State')
                        ->setCity('SA City')->process();

        return parent::process();
    }
}
