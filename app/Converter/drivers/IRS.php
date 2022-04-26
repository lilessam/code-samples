<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\IRS\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class IRS extends BaseDriver implements Driver
{
    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Sales Total',
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
            $reader->setHeaderRow(5);
            $rows = $reader->get()->toArray();
            foreach ($rows as $key => $row) {
                if ($row['invoice_date'] != null) {
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
                $address = $this->extractAddress($row['company']);
                $zip = $address->zip;
                $city = $address->city;
                $state = $address->state;

                $translator = service('translator')->setDriver('IRS')
                                                ->setName($row['company'])
                                                ->setCity($city)
                                                ->setState($state)
                                                ->setZipcode($zip)
                                                ->setAddress($row['company'])
                                                ->process();

                $this->outputRows[] = (new Model)->invoiceDate($row['invoice_date'])
                                                ->companyName($translator->output->name)
                                                ->city($translator->output->city)
                                                ->state($translator->output->state)
                                                ->zip($zip)
                                                ->salesTotal($row['sales_total'])
                                                ->commissionRate($row['comm'])
                                                ->commission($row['amount_to_pay'])
                                                ->originalCity($city)
                                                ->originalState($state)
                                                ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        $this->outputRows = service('sorter')
                    ->setRows($this->outputRows)
                    ->setCustomer('Company')
                    ->setCity('SA City')
                    ->setState('SA State')
                    ->process();

        return parent::process();
    }
}
