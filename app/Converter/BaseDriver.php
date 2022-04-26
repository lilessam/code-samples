<?php

namespace App\Converter;

use App\Models\Operation;
use Maatwebsite\Excel\Facades\Excel;
use App\Converter\Contracts\BaseDriver as AppBaseDriver;

class BaseDriver implements AppBaseDriver
{
    /**
     * The file name.
     *
     * @var string
     */
    protected $filename;

    /**
     * The rows of input.
     *
     * @var array
     */
    protected $inputRows;

    /**
     * The rows of output.
     *
     * @var array
     */
    protected $outputRows;

    /**
     * The path of input file.
     *
     * @var string
     */
    public $inputPath;

    /**
     * The path of output file.
     *
     * @var string
     */
    public $outputPath;

    /**
     * The user who processed.
     *
     * @var int
     */
    public $userId;

    /**
     * The input file name.
     *
     * @var string
     */
    public $inputFileName;

    /**
     * The operation object of the process.
     *
     * @var \App\Models\Operation;
     */
    public $operation;

    /**
     * The columns alphabets.
     *
     * @var array
     */
    public static $columns = [
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z'
    ];

    /**
     * Set the input file name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setInputFileName(string $name)
    {
        $this->inputFileName = $name;

        return $this;
    }

    /**
     * Set the user ID who took the action.
     *
     * @param integer $user_id
     *
     * @return self
     */
    public function setUser(int $user_id)
    {
        $this->userId = $user_id;

        return $this;
    }

    /**
     * Set the input properties.
     *
     * @return void
     */
    public function input(string $path)
    {
        $this->filename = pathinfo(basename($path), PATHINFO_FILENAME);
        $this->inputPath = $path;
    }

    /**
     * Process the input file and convert it.
     *
     * @return self
     */
    public function process()
    {
        $outputRows = $this->outputRows;

        Excel::create($this->filename, function ($excel) use ($outputRows) {
            $excel->sheet('Main', function ($sheet) use ($outputRows) {
                $sheet->fromArray($outputRows, null, 'A1', true);
                $sheet->row(1, function ($row) {
                    $row->setFontFamily('Comic Sans MS');
                    $row->setFontWeight(true);
                    $row->setBackground('#0da128');
                });
            });

            $this->setCurrencyColumns($excel, $outputRows);
        })->store('xls', storage_path('app/public/outputs'));

        $this->outputPath = 'storage/app/public/outputs/' . $this->filename . '.xls';

        return $this;
    }

    /**
     * Save the output file and get its path.
     * @return string
     */
    public function output() : string
    {
        return $this->outputPath;
    }

    /**
     * Log the operation into the database.
     *
     * @return \App\Models\Operation
     */
    public function log() : Operation
    {
        $operation = new Operation;
        $operation->fill([
            'input_file' => str_replace('app/public/', '', $this->inputPath),
            'output_file' => str_replace('app/public/', '', $this->outputPath),
            'client_id' => class_basename(get_called_class()),
            'user_id' => $this->userId,
            'input_file_name' => $this->inputFileName
        ]);
        $operation->save();

        return $operation;
    }

    /**
     * Set the currency columns.
     *
     * @param \Maatwebsite\Excel\Writers\LaravelExcelWriter $excel
     * @param array $rows
     *
     * @return void
     */
    public function setCurrencyColumns($excel, $rows)
    {
        if (count($rows) > 0) {
            $lines = count($rows) + 1;

            foreach ($this->currencyColumns as $column) {
                $key = static::$columns[array_search($column, array_keys($rows[0]))];
                $excel->getSheet(0)
                    ->getStyle($key . '2:' . $key . (string) $lines)
                    ->getNumberFormat()
                    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            }
        }
    }

    /**
     * Manually extract the address components.
     *
     * @param string $address
     *
     * @return array
     */
    protected function extractAddress($address)
    {
        $geocoder = service('geocoder')->setAddress($address)->process();

        return (object) [
            'city' => $geocoder->city,
            'state' => $geocoder->state,
            'zip' => $geocoder->zip
        ];
    }
}
