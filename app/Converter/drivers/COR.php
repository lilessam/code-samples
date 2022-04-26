<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\COR\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;
use Carbon\Carbon;

class COR extends BaseDriver implements Driver
{
    /**
     * The input files headers.
     *
     * @var array
     */
    private $headers = [
        'Customer name',
        'Customer Number',
        'Invoice Date',
        'Invoice Number',
        'Port Number',
        'Amount Paid',
        'Commission',
        'Commission Amount',
        'Order ID',
    ];

    /**
     * The address components.
     *
     * @var array
     */
    private $currentAddress = [
        'addressLine1' => null,
        'addressLine2' => null,
        'addressLine3' => null
    ];

    /**
     * The current parsed row.
     *
     * @var array
     */
    private $lastRow = [];

    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Amount',
        'Comm'
    ];

    /**
     * Parse the data of row string.
     *
     * @param string $row
     *
     * @return int|array
     */
    private function parseData($row)
    {
        if (str_contains($row, 'Shipped to:')) {
            $addressLine1 = explode('Shipped to:', $row)[1];
            if ($addressLine1 == '') {
                $addressLine1 = '.';
            }
            $this->currentAddress['addressLine1'] = $addressLine1;
            // Continue to next line
            return 1;
        } elseif ($this->currentAddress['addressLine1'] != null && $this->currentAddress['addressLine2'] == null && $this->currentAddress['addressLine3'] == null) {
            $this->currentAddress['addressLine2'] = $row;
            // Continue to next line
            return 2;
        } elseif ($this->currentAddress['addressLine1'] != null && $this->currentAddress['addressLine2'] != null && $this->currentAddress['addressLine3'] == null) {
            $this->currentAddress['addressLine3'] = $row;
            $this->lastRow['address'] = implode(' ', $this->currentAddress);

            return 3;
        }

        $segments = explode(' ', $row);
        $headers = array_reverse($this->headers);
        $data = [];
        $fetchedData = [];
        foreach ($headers as $key => $header) {
            $offset = count($segments) - 1 - $key;

            if ($key == 8) {
                $restOfArray = array_diff($segments, $fetchedData);
                $customerName = implode(' ', $restOfArray);
                $data[$header] = $customerName;
            } elseif ($offset < 0) {
                continue;
            } else {
                $data[$header] = $segments[$offset];
                $fetchedData[] = $segments[$offset];
            }
        }

        $this->lastRow = $data;

        return $data;
    }

    /**
     * Process the input file to extract the input rows.
     * @param string $path
     * @return self
     */
    public function input(string $path)
    {
        parent::input($path);

        foreach (['NE', 'SE'] as $tab) {
            Excel::selectSheets($tab)->load($this->inputPath, function ($reader) use ($tab) {
                $rows = $reader->noHeading()->get()->toArray();

                foreach ($rows as $key => $row) {
                    if ($key == 0) {
                        continue;
                    } else {
                        $row[0] = str_replace('  ', ' ', trim($row[0]));

                        if (str_contains($row[0], 'Totals for')) {
                            continue;
                        }

                        $data = $this->parseData($row[0]);
                        $this->lastRow['lines'][] = $key;
                        // Line 1
                        if ($data == 1) {
                            continue;
                        } elseif ($data == 2) {
                            continue;
                        } elseif ($data == 3) {
                            $this->inputRows[$tab][] = $this->lastRow;

                            // Re-Initialize the data properties.
                            $this->lastRow = [];
                            $this->currentAddress = [
                                'addressLine1' => null,
                                'addressLine2' => null,
                                'addressLine3' => null
                            ];
                            continue;
                        }
                    }
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
        $this->outputRows['NE'] = [];
        $this->outputRows['SE'] = [];

        foreach (['NE', 'SE'] as $tab) {
            foreach ($this->inputRows[$tab] as $key => $row) {
                try {
                    $address = $this->extractAddress(trim($row['address']));
                    $city = $address->city;
                    $state = $address->state;
                    $translator = service('translator')->setDriver('COR')
                                                    ->setName($row['Customer name'])
                                                    ->setCity($city)
                                                    ->setState($state)
                                                    ->setAddress($row['address'])
                                                    ->process();

                    $generatedRow = (new Model)->invoiceDate($row['Invoice Date'])
                    ->invoiceNumber($row['Invoice Number'])
                    ->part($row['Port Number'])
                    ->customerName($translator->output->name)
                    ->city($translator->output->city)
                    ->state($translator->output->state)
                    ->amount($row['Amount Paid'])
                    ->commissionRate($row['Commission'])
                    ->commission($row['Commission Amount'])
                    ->originalCity($city)
                    ->originalState($state)
                    ->toArray();

                    $temp = $this->outputRows[$tab];
                    $temp[] = $generatedRow;

                    $this->outputRows[$tab] = $temp;

                    unset($generatedRow);

                } catch (Exception $e) {

                    if (!isset($this->inputRows[$tab][$key - 1])) {
                        throw $e;
                    }

                    $content = $this->inputRows[$tab][$key - 1]['address'];
                    $line = $this->inputRows[$tab][$key - 1]['lines'][count($this->inputRows[$tab][$key - 1]['lines']) - 1];
                    throw new UsefulException($e, $line, [
                        'content' => $content,
                        'tab' => $tab
                    ]);
                }
            }
        }

        $this->outputRows = service('sorter')
                            ->setRows(array_merge($this->outputRows['NE'], $this->outputRows['SE']))
                            ->setCustomer('Customer')
                            ->setCity('D-Macs City')
                            ->setState('Ship State')
                            ->process();

        return parent::process();
    }
}
