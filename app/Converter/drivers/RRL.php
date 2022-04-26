<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\RRL\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class RRL extends BaseDriver implements Driver
{
    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Unit $',
        'Sales',
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

        Excel::selectSheetsByIndex(2)->load($this->inputPath, function ($reader) {
            $rows = $reader->get()->toArray();
            foreach ($rows as $key => $row) {
                unset($row['sales_solutions_inc.']);

                if ($row[0] != null) {
                    $row['line'] = $key;
                    $this->inputRows[] = $row;
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
        foreach ($this->inputRows as $row) {
            try {
                $city = $this->extractAddress($row['_3'] . ' ' . $row['_2'])->city;

                $translator = service('translator')->setDriver('RRL')
                                                ->setName($row['_4'])
                                                ->setCity($city)
                                                ->setState($row['_2'])
                                                ->setAddress($row['_3'] . ' ' . $row['_2'])
                                                ->process();

                $this->outputRows[] = (new Model)->date($row[0])
                                                ->invoice($row['_1'])
                                                ->product($row['_5'])
                                                ->qty($row['_6'])
                                                ->unit($row['_7'])
                                                ->distributor($translator->output->name)
                                                ->city($translator->output->city)
                                                ->state($translator->output->state)
                                                ->sales($row['_8'])
                                                ->commission($row['_9'])
                                                ->originalCity($city)
                                                ->originalState($row['_2'])
                                                ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        $this->outputRows = service('sorter')
                            ->setRows($this->outputRows)
                            ->setCustomer('Distributor')
                            ->setCity('SA City')
                            ->setState('SA State')
                            ->process();

        return parent::process();
    }
}
