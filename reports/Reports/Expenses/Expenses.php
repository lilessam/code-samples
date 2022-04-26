<?php

namespace Reports\Reports\Expenses;

use Reports\Reports\Report;
use Reports\Contracts\Report as ReportInterface;

class Expenses extends Report implements ReportInterface
{
    /**
     * The name of the report.
     *
     * @var string
     */
    protected $name = 'expenses';

    /**
     * The view of the report.
     *
     * @var string
     */
    protected $view_path = 'Expenses::expenses';

    /**
     * The printable PDF template for the report.
     *
     * @var string
     */
    protected $pdf_template_path = 'Expenses::expenses_pdf';

    /**
     * The printable PDF template for the report.
     *
     * @var string
     */
    protected $word_template_path = 'Expenses::expenses_word';
}
