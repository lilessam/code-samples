<?php

namespace Reports\Reports\TaskSummary;

use Reports\Reports\Report;
use Reports\Contracts\Report as ReportInterface;

class TaskSummary extends Report implements ReportInterface
{
    /**
     * The name of the report.
     *
     * @var string
     */
    protected $name = 'task_summary';

    /**
     * The view of the report.
     *
     * @var string
     */
    protected $view_path = 'TaskSummary::task_summary';

    /**
      * The printable PDF template for the report.
      *
      * @var string
      */
    protected $pdf_template_path = 'TaskSummary::task_summary_pdf';

    /**
     * The printable PDF template for the report.
     *
     * @var string
     */
    protected $word_template_path = 'TaskSummary::task_summary_word';
}
