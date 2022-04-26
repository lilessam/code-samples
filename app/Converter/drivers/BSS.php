<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\BSS\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class BSS extends BaseDriver implements Driver
{
    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Price',
        'Product Total',
        'Comm'
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
            $reader->setHeaderRow(4);
            $rows = $reader->get()->toArray();
            foreach ($rows as $key => $row) {
                if ($row['independent_rep'] != null && $row['cust_name'] != null) {
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
                $address = $this->extractAddress($row[0]);
                $city = $address->city;
                $state = $address->state;
                $zipcode = $address->zip;

                $translator = service('translator')->setDriver('BSS')
                                                ->setName($row['cust_name'])
                                                ->setCity($city)
                                                ->setState($state)
                                                ->setZipcode($zipcode)
                                                ->setAddress($row[0])
                                                ->process();

                $this->outputRows[] = (new Model)->invoiceNum($row['invcm_num'])
                                    ->docDate($row['doc_date'])
                                    ->itemCode($row['item_code'])
                                    ->itemDescription($row['item_description'])
                                    ->qty($row['quantity'])
                                    ->price($row['price'])
                                    ->customerName($translator->output->name)
                                    ->city($translator->output->city)
                                    ->state($translator->output->state)
                                    ->productTotal($row['product_total'])
                                    ->commission($row['comm_to_pay'])
                                    ->originalAddress($row[0])
                                    ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        $this->outputRows = service('sorter')
                            ->setRows($this->outputRows)
                            ->setCustomer('Cust Name')
                            ->setCity('SA City')
                            ->setState('SA State')
                            ->process();

        return parent::process();
    }
}
