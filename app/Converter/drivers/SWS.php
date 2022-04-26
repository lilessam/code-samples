<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\SWS\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class SWS extends BaseDriver implements Driver
{
    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Unit Price',
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

        Excel::selectSheetsByIndex(0)->load($this->inputPath, function ($reader) {
            $reader->setHeaderRow(1);
            $rows = $reader->get()->toArray();
            foreach ($rows as $key => $row) {
                if ($row['customer'] != null) {
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

                if ($row['inv'] == null) {
                    continue;
                }

                $address = $this->extractAddress($row['ship_to']);
                $city = $address->city;
                $state = $address->state;
                $translator = service('translator')
                                ->setDriver('SWS')
                                ->setName($row['customer'])
                                ->setCity($city)
                                ->setState($state)
                                ->setAddress($row['ship_to'])
                                ->process();

                $this->outputRows[] = (new Model)->invoice($row['inv'])
                                                ->customerPo($row['customer_po'])
                                                ->itemCode($row['item_code'])
                                                ->description($row['description'])
                                                ->unitPrice($row['unit_price'])
                                                ->quantity($row['quantity'])
                                                ->name($translator->output->name)
                                                ->city($translator->output->city)
                                                ->state($translator->output->state)
                                                ->sales($row['total_revenue'])
                                                ->commissionRate($row['comm_rate'])
                                                ->commission($row['commission'])
                                                ->originalShipTo($row['ship_to'])
                                                ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        $this->outputRows = service('sorter')
                            ->setRows($this->outputRows)
                            ->setCustomer('SA Customer')
                            ->setCity('SA City')
                            ->setState('SA State')
                            ->process();

        return parent::process();
    }
}
