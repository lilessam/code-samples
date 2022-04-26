<?php

namespace Reports\Reports\CostSummary;

use Reports\Reports\Report;
use Reports\Contracts\Report as ReportInterface;

class CostSummary extends Report implements ReportInterface
{
    /**
     * The name of the report.
     *
     * @var string
     */
    protected $name = 'cost_summary';

    /**
     * The view of the report.
     *
     * @var string
     */
    protected $view_path = 'CostSummary::cost_summary';

    /**
     * The printable PDF template for the report.
     *
     * @var string
     */
    protected $pdf_template_path = 'CostSummary::cost_summary_pdf';

    /**
     * The printable PDF template for the report.
     *
     * @var string
     */
    protected $word_template_path = 'CostSummary::cost_summary_word';
}
