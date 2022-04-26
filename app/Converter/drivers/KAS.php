<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\KAS\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class KAS extends BaseDriver implements Driver
{
    /**
     * @var array
     */
    private $totals = [
        'sale' => 0,
        'comm' => 0
    ];

    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Sale',
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
            $reader->setHeaderRow(6);
            $rows = $reader->get()->toArray();
            foreach ($rows as $key => $row) {
                if ($row['date'] != null) {
                    $this->totals['sale'] = $this->totals['sale'] + (float) $row['sales'];
                    $this->totals['comm'] = $this->totals['comm'] + (float) $row['calculated_amount'];
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
                $address = $this->extractAddress($row['ship_to']);
                $city = $address->city;
                $state = $address->state;

                $translator = service('translator')->setDriver('KAS')
                                                ->setName($row['company'])
                                                ->setCity($city)
                                                ->setState($state)
                                                ->setAddress($row['ship_to'])
                                                ->process();
                //
                $this->outputRows[] = (new Model)->date($row['date'])
                                                ->invoice($row['invoice'])
                                                ->po($row['po'])
                                                ->name($translator->output->name)
                                                ->city($translator->output->city)
                                                ->state($translator->output->state)
                                                ->sales($row['sales'])
                                                ->commission($row['calculated_amount'])
                                                ->originalCity($city)
                                                ->originalState($state)
                                                ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        $this->outputRows = service('sorter')
                            ->setRows($this->outputRows)
                            ->setCustomer('Bill To')
                            ->setCity('SA City')
                            ->setState('SA State')
                            ->process();

        $this->outputRows[] = (new Model)->date(null)
                                ->invoice(null)
                                ->po(null)
                                ->name(null)
                                ->city(null)
                                ->state(null)
                                ->sales($this->totals['sale'])
                                ->commission($this->totals['comm'])
                                ->dontCalculateCommissionRate(true)
                                ->toArray();

        return parent::process();
    }
}
