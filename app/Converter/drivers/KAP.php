<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\KAP\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class KAP extends BaseDriver implements Driver
{
    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Unit',
        'Dollars',
        'Comm'
    ];

    /**
     * The zipcode.
     *
     * @var array
     */
    private $currentZip = null;

    /**
     * The address.
     *
     * @var array
     */
    private $currentAddress = null;

    /**
     * The customer ID.
     *
     * @var string
     */
    private $currentCustomer = null;

    /**
     * Determine whether the parser mode should
     * parse Vallen rows.
     * @var boolean
     */
    private $vallenMode = false;

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
                if ($row[0] != null) {
                    $parsedRow['line'] = $key;

                    if (str_contains($row[0], 'VALLEN') && !$this->vallenMode) {
                        $this->vallenMode = true;
                        continue;
                    }

                    if (str_contains(strtolower($row[0]), 'ship to:')) {
                        $this->currentAddress = $row[0];
                        $line = explode('SHIP TO:', $row[0])[1];
                        $line = explode(' -', $line)[0];
                        $line = explode(' ', $line)[count(explode(' ', $line)) - 1];
                        $this->currentZip = is_numeric($line) ? substr($line, 0, 5) : $this->currentZip;
                        continue;
                    } elseif (str_contains(strtolower($row[0]), 'customer:')) {
                        $this->currentCustomer = explode('CUSTOMER:', $row[0])[1];
                        continue;
                    } elseif (str_contains(strtolower($row[0]), 'invoice')) {
                        continue;
                    } else {
                        $address = $this->extractAddress($this->currentZip);
                        $items = explode(' ', $row[0]);
                        if (count($items) == 8 && starts_with($items[0], 'INV')) {
                            $parsedRow['inv'] = $items[0];
                            $parsedRow['cust_id'] = $items[1];
                            $parsedRow['product'] = $items[2];
                            $parsedRow['sku'] = $items[5];
                            $parsedRow['qty'] = $items[3];
                            $parsedRow['unit'] = $items[4];
                            $parsedRow['age_used'] = $items[6]/100;
                            $parsedRow['comm'] = $items[7];
                            $parsedRow['sales'] = (float) $parsedRow['qty'] * (float) $parsedRow['unit'];
                            $parsedRow['sa_city'] = $address->city;
                            $parsedRow['state'] = $address->state;
                            $parsedRow['zip'] = $this->currentZip;
                            $parsedRow['address'] = $this->currentAddress;
                        }
                    }
                    $this->inputRows[] = $parsedRow;
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
                $translator = service('translator')->setDriver('KAP')
                                                ->setName($row['cust_id'])
                                                ->setCity($row['sa_city'])
                                                ->setState($row['state'])
                                                ->process();
                $this->outputRows[] = (new Model)->customerId($row['cust_id'])
                                                ->invoiceId($row['inv'])
                                                ->product($row['product'])
                                                ->sku($row['sku'])
                                                ->qty($row['qty'])
                                                ->unit($row['unit'])
                                                ->distributor($translator->output->name)
                                                ->city($translator->output->city)
                                                ->state($translator->output->state)
                                                ->sales($row['sales'])
                                                ->commissionRate($row['age_used'])
                                                ->commission($row['comm'])
                                                ->originalZip($row['zip'])
                                                ->originalAddress($row['address'])
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
