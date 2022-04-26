<?php

namespace Reporter\Drivers;

use PhpOffice\PhpWord\PhpWord;
use Reporter\Contracts\Driver;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;

class Word2007 implements Driver
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
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * @var \PhpOffice\PhpWord\PhpWord
     */
    protected $phpword;

    /**
     * Make a new instance of Word2007
     *
     * @param \Illuminate\Contracts\View\Factory $view
     * @return void
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
        $this->phpword = new PhpWord;
    }

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
     * @return self
     */
    public function generate()
    {
        $templateContent = $this->view->make($this->template, $this->data)->render();
        // Adding an empty Section to the document...
        $section = $this->phpword->addSection();
        // Adding Text element to the Section having font styled by default...
        @\PhpOffice\PhpWord\Shared\Html::addHtml($section, $templateContent, false, false);

        return $this;
    }

    /**
     * Generate the document.
     *
     * @return self
     */
    public function saveDocument()
    {
        $writer = IOFactory::createWriter($this->phpword, 'Word2007');
        $writer->save(public_path('/reports/generated/' . $this->name));

        return $this;
    }

    /**
     * Download the report file.
     *
     * @return Binary
     */
    public function download()
    {
        $this->generate()->saveDocument();

        return response()->download(public_path('/reports/generated/' . $this->name), $this->name);
    }
}
