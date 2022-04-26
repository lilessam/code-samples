<?php

namespace Reports\Contracts;

interface Report
{
    /**
     * Set the data of the report.
     *
     * @param array $data
     * @return self
     */
    public function data($data);

    /**
     * Get the report data.
     *
     * @return array
     */
    public function getData();

    /**
     * Return the printable PDF resource path.
     *
     * @return string
     */
    public function getPDFTemplate();

    /**
     * Return the printable Word resource path.
     *
     * @return string
     */
    public function getWordTemplate();

    /**
     * Generate the markup view of the report.
     *
     * @return string
     */
    public function view();

    /**
     * Export the report in a PDF or Doc.
     * @param string $file_name
     * @param string $format
     * @return Binary
     */
    public function download(string $file_name, string $format);
}
