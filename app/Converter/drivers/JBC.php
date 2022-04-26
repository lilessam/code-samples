<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\JBC\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class JBC extends BaseDriver implements Driver
{
    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'D-Macs Sales',
        'Commission'
    ];

    /**
     * Process the input file to extract the input rows.
     * @param string $path
     * @return self
     */
    public function input(string $path)
    {
        parent::input($path);

        Excel::load($this->inputPath, function ($reader) {
            $rows = $reader->get()->toArray();
            foreach ($rows as $key => $row) {
                if ($row['payment_date'] != null) {
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
        foreach ((array) $this->inputRows as $row) {
            try {
                $address = $this->extractAddress($row['ship_to_address']);
                $city = $address->city;
                $state = $address->state;

                $translator = service('translator')->setDriver('JBC')
                                                ->setName($row['customer_name'])
                                                ->setCity($city)
                                                ->setState($state)
                                                ->setAddress($row['ship_to_address'])
                                                ->process();

                $this->outputRows[] = (new Model)->invoiceDate($row['invoice_date'])
                                                ->customerName($translator->output->name)
                                                ->city($translator->output->city)
                                                ->state($translator->output->state)
                                                ->sales($row['invoice_amount'])
                                                ->freight($row['freight'])
                                                ->commissionRate($row[0])
                                                ->commission($row['invoice_amount'])
                                                ->originalCity($city)
                                                ->originalState($state)
                                                ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        $this->outputRows = service('sorter')
                            ->setRows($this->outputRows)
                            ->setCustomer('Customer Name')
                            ->setCity('D-Macs City')
                            ->setState('SA State')
                            ->process();

        return parent::process();
    }
}
