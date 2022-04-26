<?php

namespace Reports\Reports;

use Reports\Contracts\Report as ReportInterface;

class Report implements ReportInterface
{
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Report data array.
     *
     * @var array
     */
    protected $data;

    /**
     * Make a new instance of CostSummary
     *
     * @param \Illuminate\Contracts\View\Factory $view
     * @return void
     */
    public function __construct($view)
    {
        $this->view = $view;
    }

    /**
     * Set the data of the report.
     *
     * @param array $data
     * @return self
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the report data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return the printable PDF resource path.
     *
     * @return string
     */
    public function getPDFTemplate()
    {
        return $this->pdf_template_path;
    }

    /**
     * Return the printable Word resource path.
     *
     * @return string
     */
    public function getWordTemplate()
    {
        return $this->word_template_path;
    }

    /**
     * Generate the markup view of the report.
     *
     * @return string
     */
    public function view()
    {
        return $this->view->make($this->view_path, $this->data)->render();
    }

    /**
     * Export the report in a PDF or Doc.
     * @param string $file_name
     * @param string $format
     * @return Binary
     */
    public function download(string $file_name, string $format)
    {
        if ($format == 'pdf') {
            return download_report($file_name, $this->pdf_template_path, $this->data, $format);
        } else {
            return download_report($file_name, $this->word_template_path, $this->data, $format);
        }
    }

    /**
     * Streams the report in a PDF or Doc.
     * @param string $file_name
     * @param string $format
     * @return Binary
     */
    public function stream(string $file_name, string $format)
    {
        if ($format == 'pdf') {
            return stream_report($file_name, $this->pdf_template_path, $this->data, $format);
        } else {
            return stream_report($file_name, $this->word_template_path, $this->data, $format);
        }
    }
}
