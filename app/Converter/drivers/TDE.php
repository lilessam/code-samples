<?php

namespace App\Converter\Drivers;

use Exception;
use Carbon\Carbon;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\TDE\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class TDE extends BaseDriver implements Driver
{
    /**
     * The date of the last day of the previous month
     * in the cover tab.
     *
     * @var mixed
     */
    private $coverMonth;

    /**
     * The tabs of TDE Import.
     *
     * @var array
     */
    private $tabs = [
        [
            'name' => '219 SALES SOL',
            'header_row' => 1,
            'conditioner' => 'inv._date'
        ],
        [
            'name' => 'abc',
            'header_row' => null,
            'conditioner' => 0
        ]
    ];

    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'UNIT$',
        'Total Cost',
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

        Excel::selectSheetsByIndex(0)->load($this->inputPath, function ($reader) {
            $rows = $reader->noHeading()->get()->toArray();
            foreach ($rows as $key => $row) {
                if ($key == 2) {
                    $this->coverMonth = Carbon::parse($row[5]);
                }
            }
        });

        foreach ($this->tabs as $tab) {
            Excel::selectSheets($tab['name'])->load($this->inputPath, function ($reader) use ($tab) {

                if ($tab['header_row']) {
                    $reader->setHeaderRow($tab['header_row']);
                } else {
                    $reader->noHeading();
                }

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
     * Process the rows from 219 SALES SOL tab.
     *
     * @param array $row
     *
     * @return array
     */
    private function process2ndTab($row)
    {
        $address = $this->extractAddress($row['ship_to']);
        $city = $address->city;
        $state = $address->state;

        try {
            $translator = service('translator')
                            ->setDriver('TDE')
                            ->setName($row['customer_name'])
                            ->setCity($city)
                            ->setState($state)
                            ->setAddress($row['ship_to'])
                            ->process();

            return (new Model)->invoice((string) $row['inv.'])
                            ->invoiceDate($row['inv._date'])
                            ->part($row['part'])
                            ->partDesc($row['part_desc.'])
                            ->quantity($row['ship_qty.'])
                            ->unitPrice($row['unit'])
                            ->name($translator->output->name)
                            ->city($translator->output->city)
                            ->state($translator->output->state)
                            ->sales($row['ext.'])
                            ->commissionRate($row['part_1'] * 100)
                            ->commission((float) $row['ext.'] * $row['part_1'])
                            ->originalCity('')
                            ->originalState('')
                            ->originalShipTo($row['ship_to'])
                            ->toArray();
        } catch (Exception $e) {
            throw new UsefulException($e, $row['line'], [
                'tab' => $this->tabs[1]['name']
            ]);
        }
    }

    /**
     * Process the rows from abc tab.
     *
     * @param array $row
     *
     * @return array
     */
    private function process3rdTab($row)
    {
        try {
            $parts = explode('#', $row[3]);
            $translator = service('translator')
                            ->setDriver('TDE')
                            ->setName('ABC Direct')
                            ->setCity($row[1])
                            ->setState($row[2])
                            ->process();

            return (new Model)->invoice('')
                            ->invoiceDate(Carbon::parse($this->coverMonth)->lastOfMonth())
                            ->part($parts[1])
                            ->partDesc($parts[0])
                            ->quantity(0)
                            ->unitPrice(0)
                            ->name($translator->output->name)
                            ->city($translator->output->city)
                            ->state($translator->output->state)
                            ->sales($row[4])
                            ->commissionRate(5)
                            ->commission((float) $row[4] / 100 * 5)
                            ->originalCity($row[1])
                            ->originalState($row[2])
                            ->toArray();
        } catch (Exception $e) {
            throw new UsefulException($e, $row['line'], [
                'tab' => $this->tabs[2]['name']
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
                switch ($tab_key) {
                    case 0:
                        $parsed_row = $this->process2ndTab($row);
                        break;
                    case 1:
                        $parsed_row = $this->process3rdTab($row);
                        break;
                }

                $this->outputRows[] = $parsed_row;
            }
        }

        $this->outputRows = service('sorter')
                            ->setRows($this->outputRows)
                            ->setCustomer('CUSTOMER NAME')
                            ->setCity('SA City')
                            ->setState('SA State')
                            ->process();

        return parent::process();
    }
}
