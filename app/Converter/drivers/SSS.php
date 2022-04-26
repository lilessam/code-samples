<?php

namespace App\Converter\Drivers;

use Exception;
use Carbon\Carbon;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\SSS\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;
use App\Converter\Exceptions\FormattingException;

class SSS extends BaseDriver implements Driver
{
    /**
     * The date of grainger.
     *
     * @var \Carbon\Carbon
     */
    private $graingerDate;

    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Unit $',
        'Amt Total',
        'Comm',
    ];

    /**
     * Process the input file to extract the input rows.
     * @param string $path
     * @return self
     */
    public function input(string $path)
    {
        parent::input($path);

        Excel::selectSheets('Commission Report')->load($this->inputPath, function ($reader) {
            $reader->setHeaderRow(11);
            $rows = $reader->get()->toArray();
            foreach ($rows as $key => $row) {
                if (in_array('invoice_date', array_keys($row)) && $row['invoice_date'] != null) {
                    $row['line'] = $key;
                    $this->inputRows['cr'][] = $row;
                }
            }
        });

        Excel::selectSheets('GRAINGER')->load($this->inputPath, function ($reader) {
            $reader->setHeaderRow(9);
            $rows = $reader->get()->toArray();

            $date = $reader->getActiveSheet()->getCell('H6')->getValue();
            if ($date == null || $date == '') {
                throw new FormattingException('The date of Grainger tab should be in H6 Cell');
            }

            $this->graingerDate = Carbon::parse($date);

            foreach ($rows as $key => $row) {
                if (in_array('rep', array_keys($row)) && $row['rep'] != null) {
                    $row['line'] = $key;
                    $this->inputRows['grainger'][] = $row;
                }
            }
        });

        Excel::selectSheets('Credit Memo')->load($this->inputPath, function ($reader) {
            $rows = $reader->get()->toArray();
            foreach ($rows as $key => $row) {
                if (key_exists('_2', $row) && $row['_2'] != null && $key != 0) {
                    $row['line'] = $key;
                    $this->inputRows['credit'][] = $row;
                }
            }
        });

        return $this;
    }

    /**
     * Process the input file and convert it.
     *
     * @return self
     */
    public function process()
    {
        foreach ($this->inputRows['cr'] as $row) {
            try {
                $address = $this->extractAddress($row['ship_to_state_and_zip']);
                $city = $address->city;
                $state = $address->state;
                $zip = $address->zip;

                $translator = service('translator')
                                ->setDriver('SSS')
                                ->setName($row['customer_name'])
                                ->setCity($city)
                                ->setState($state)
                                ->setZipcode($zip)
                                ->setAddress($row['ship_to_state_and_zip'])
                                ->process();

                $this->outputRows[] = (new Model)->invoiceDate($row['invoice_date'])
                                                ->name($translator->output->name)
                                                ->city($translator->output->city)
                                                ->state($translator->output->state)
                                                ->product($row['product'])
                                                ->quantity($row['qty'])
                                                ->unitPrice($row['amt_each'])
                                                ->sales($row['amt_total'])
                                                ->originalCity($city)
                                                ->originalState($state)
                                                ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        foreach ($this->inputRows['grainger'] as $row) {
            try {
                $address = explode(',', $row['city_state']);
                $state = $address[count($address) - 1];
                $city = $address[count($address) - 2];
            } catch (Exception $e) {
                throw new FormattingException(sprintf("The City & State column doesn't contain a correct value."));
            }

            if (!is_numeric($row['qty'])) {
                throw new FormattingException(sprintf("The Quantity column doesn't have a numeric value."));
            }

            if (!preg_match('/\\d/', $row['zip_code']) > 0) {
                throw new FormattingException(sprintf("The Zipcode column doesn't have a numeric value."));
            }

            try {
                $translator = service('translator')
                                ->setDriver('SSS')
                                ->setCity($city)
                                ->setState($state)
                                ->setName('Grainger')
                                ->process();

                $this->outputRows[] = (new Model)->invoiceDate($this->graingerDate->lastOfMonth())
                                            ->name($translator->output->name)
                                            ->city($translator->output->city)
                                            ->state($translator->output->state)
                                            ->product($row['part'])
                                            ->description($row['description'])
                                            ->quantity($row['qty'])
                                            ->unitPrice((float) ($row['cost'] / $row['qty']))
                                            ->sales($row['cost'])
                                            ->originalCity($city)
                                            ->originalState($state)
                                            ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        foreach ($this->inputRows['credit'] as $row) {
            try {
                $translator = service('translator')
                                ->setDriver('SSS')
                                ->setName($row['_1'])
                                ->process();

                $this->outputRows[] = (new Model)->invoiceDate($row['_2'])
                                            ->name($translator->output->name)
                                            ->city('')
                                            ->state('')
                                            ->product('')
                                            ->description($row[0])
                                            ->quantity('')
                                            ->unitPrice('')
                                            ->sales(-$row['sumc3c40'])
                                            ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        $this->outputRows = service('sorter')
                            ->setRows($this->outputRows)
                            ->setCustomer('Customer Name')
                            ->setCity('SA City')
                            ->setState('Ship to State')
                            ->process();

        return parent::process();
    }
}
