<?php

namespace App\Converter\Drivers;

use Exception;
use App\Converter\BaseDriver;
use App\Converter\Contracts\Driver;
use App\Converter\Drivers\REM\Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Exceptions\UsefulException;

class REM extends BaseDriver implements Driver
{
    /**
     * @var string
     */
    private $processingMonth;

    /**
     * The columns that will be formatted as currency.
     *
     * @var array
     */
    protected $currencyColumns = [
        'Sales',
        'Comm Paid',
    ];

    /**
     * Process the input file to extract the input rows.
     * @param string $path
     * @return self
     */
    public function input(string $path)
    {
        parent::input($path);

        Excel::load($this->inputPath, function ($reader) use ($path) {
            Excel::selectSheets($reader->reader->listWorksheetInfo($reader->file)[0]['worksheetName'])->load($path, function ($reader) {
                $date = explode(' ', $reader->getSheetInfoForActive()['worksheetName']);
                array_shift($date);
                $date = implode(' ', $date);
                $this->processingMonth = \Carbon\Carbon::parse($date)->format('M Y');
                $reader->setHeaderRow(9);
                $rows = $reader->get()->toArray();

                foreach ($rows as $key => $row) {
                    if ($row['customer'] != null && $row['invoices_paid'] != null) {
                        $row['line'] = $key;
                        $this->inputRows[] = $row;
                    }
                }
            });
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
                $city = key_exists('city', $row) ? $row['city'] : null;

                $translator = service('translator')->setDriver('REM')
                    ->setName($row['customer'])
                    ->setCity($city)
                    ->setState($row['ship_to'])
                    ->process();

                $this->outputRows[] = (new Model)->month($this->processingMonth)
                    ->date($row['date'])
                    ->invoice($row['inv'])
                    ->customer($translator->output->name)
                    ->city($translator->output->city)
                    ->state($translator->output->state)
                    ->sales($row['amount'])
                    ->commissionRate($row['comm'])
                    ->commission($row['invoices_paid'])
                    ->originalCity($city)
                    ->originalState($row['ship_to'])
                    ->toArray();
            } catch (Exception $e) {
                throw new UsefulException($e, $row['line']);
            }
        }

        $this->outputRows = service('sorter')
            ->setRows($this->outputRows)
            ->setCustomer('Customer')
            ->setCity('SA City')
            ->setState('Ship To')
            ->process();

        return parent::process();
    }
}
