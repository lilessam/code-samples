<?php

namespace Reporter\Drivers;

use Reporter\Contracts\Driver;
use Barryvdh\Snappy\Facades\SnappyPdf as PDFFacade;

class PDF implements Driver
{
    /**
     * The template path in resources folder.
     *
     * @var string
     */
    protected $template;

    /**
     * The report data for the report.
     *
     * @var mixed
     */
    protected $data;

    /**
     * The name of the generated file.
     *
     * @var string
     */
    protected $name;

    /**
     * Load the template for the report.
     * @param  string  $path
     * @return self
     */
    public function template($path)
    {
        $this->template = $path;

        return $this;
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Load the data & variables for the report.
     * @param  mixed  $data
     * @return self
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the data and variables of the report.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the name of the generated file.
     *
     * @param  string  $name
     * @return self
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * get the name of the generated file.
     *
     * @return string
     */
    public function getName($name)
    {
        return $this->name;
    }

    /**
     * Generate the report.
     *
     * @return void
     */
    public function generate()
    {
        return PDFFacade::loadView($this->template, $this->data);
    }

    /**
     * Download the report file.
     *
     * @return Binary
     */
    public function download()
    {
        return $this->generate()->download($this->name);
    }
}
