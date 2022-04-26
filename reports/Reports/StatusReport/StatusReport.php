<?php

namespace Reports\Reports\StatusReport;

use Reports\Reports\Report;
use Reports\Contracts\Report as ReportInterface;

class StatusReport extends Report implements ReportInterface
{
    /**
     * The name of the report.
     *
     * @var string
     */
    protected $name = 'status_report';

    /**
     * The view of the report.
     *
     * @var string
     */
    protected $view_path = 'StatusReport::status_report';

    /**
      * The printable PDF template for the report.
      *
      * @var string
      */
    protected $pdf_template_path = 'StatusReport::status_report_pdf';

    /**
     * The printable PDF template for the report.
     *
     * @var string
     */
    protected $word_template_path = 'StatusReport::status_report_word';
}
