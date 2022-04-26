<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\SLI\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class SLI extends BaseDriver implements Driver
{
    /**
     * The tabs of TDE Import.
     *
     * @var array
     */
    private $tabs = [
        [
            'name' => 'Slice',
            'header_row' => 1,
            'conditioner' => 'name',
        ],
        [
            'name' => 'Grainger',
            'header_row' => 6,
            'conditioner' => 'state_ship_to'
        ],
    ];

    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Unit Price',
        'Amount',
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

        foreach ($this->tabs as $tab) {
            Excel::selectSheets($tab['name'])->load($this->inputPath, function ($reader) use ($tab) {
                $reader->setHeaderRow($tab['header_row']);
                $rows = $reader->get()->toArray();
                foreach ($rows as $key => $row) {
                    $valid = true;
                    if (in_array('conditioner', array_keys($tab))) {
                        if ($row[$tab['conditioner']] == null) {
                            $valid = false;
                        }
                    }
                    if (!$valid) {
                        continue;
                    }

                    $row['line'] = $key;
                    $this->inputRows[$tab['name']][] = $row;
                }
            });
        }

        return $this;
    }

    /**
     * Parse Slice tab rows.
     *
     * @param array $row
     *
     * @return array
     */
    private function processSliceRows($row)
    {
        try {
            $translator = service('translator')->setDriver('SLI')
                                            ->setName($row['name'])
                                            ->setCity($row['shipping_city'])
                                            ->setState($row['shipping_stateprovince'])
                                            ->process();

            return (new Model)->date($row['date'])
                            ->name($translator->output->name)
                            ->city($translator->output->city)
                            ->state($translator->output->state)
                            ->item($row['item'])
                            ->memo($row['memo'])
                            ->quantity($row['quantity'])
                            ->unit($row['unit_price'])
                            ->amount($row['amount'])
                            ->originalCity($row['shipping_city'])
                            ->originalState($row['shipping_stateprovince'])
                            ->toArray();
        } catch (Exception $e) {
            throw new UsefulException($e, $row['line'], [
                'tab' => $this->tabs[0]['name']
            ]);
        }
    }

    /**
     * Parse Grainger tab rows.
     *
     * @param array $row
     *
     * @return array
     */
    private function processGraingerRows($row)
    {
        try {
            list($city, $state) = explode(',', $row['_1']);
            $state = trim($state);

            $translator = service('translator')->setDriver('SLI')
                                            ->setName('Grainger')
                                            ->setCity($city)
                                            ->setState($state)
                                            ->process();

            return (new Model)->date($row['cal._year_month'])
                            ->name($translator->output->name)
                            ->city($translator->output->city)
                            ->state($translator->output->state)
                            ->item($row['material'])
                            ->memo($row['_2'])
                            ->quantity($row['quantity_sold'])
                            ->unit(null)
                            ->amount($row['cost_of_product_sold'])
                            ->originalCity($city)
                            ->originalState($state)
                            ->toArray();
        } catch (Exception $e) {
            throw new UsefulException($e, $row['line'], [
                'tab' => $this->tabs[1]['name']
            ]);
        }
    }

    /**
     * Process the input file and convert it.
     *
     * @return self
     */
    public function process()
    {
        foreach (collect($this->tabs) as $tab_key => $tab) {
            foreach ($this->inputRows[$tab['name']] as $row) {
                if ($tab_key == 0) {
                    if (str_contains(strtolower($row['name']), 'grainger')) {
                        continue;
                    }
                    $parsed_row = $this->processSliceRows($row);
                } elseif ($tab_key == 1) {
                    $parsed_row = $this->processGraingerRows($row);
                }

                $this->outputRows[] = $parsed_row;
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
