<?php

namespace Reporter\Contracts;

interface Driver
{
    /**
     * Load the template for the report.
     * @param  string  $path
     * @return self
     */
    public function template($path);

    /**
     * Get the template path.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Load the data & variables for the report.
     * @param  mixed  $data
     * @return self
     */
    public function data($data);

    /**
     * Get the data and variables of the report.
     *
     * @return mixed
     */
    public function getData();

    /**
     * Set the name of the generated file.
     *
     * @param  string  $name
     * @return self
     */
    public function name($name);

    /**
     * get the name of the generated file.
     *
     * @return string
     */
    public function getName($name);

    /**
     * Generate the report.
     *
     * @return void
     */
    public function generate();

    /**
     * Download the report file.
     *
     * @return Binary
     */
    public function download();
}
