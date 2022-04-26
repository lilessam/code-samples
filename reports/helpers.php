<?php

use Reports\Report;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\Snappy\Facades\SnappyPdf as PDFFacade;

if (!function_exists('reports')) {
    /**
     * Make a new report instance.
     *
     * @return \Reports\Report
     */
    function reports()
    {
        return app(Report::class);
    }
}

if (!function_exists('combine_reports_pdf')) {
    /**
     * Combine different reports in one PDF
     *
     * @param array $reports ['view' => 'data']
     * @param string $file
     * @return \Illuminate\Http\Response
     */
    function combine_reports_pdf($reports, $file)
    {
        $viewFactory = app()['view'];
        $reportContent = '';
        foreach ($reports as $template => $data) {
            $reportContent .= $viewFactory->make($template, $data)->render();
        }

        return PDFFacade::loadHTML($reportContent)->download($file);
    }
}

if (!function_exists('combine_reports_pdf_and_save')) {
    /**
     * @deprecated 1.0.0
     * Combine different reports in one PDF and save the file without
     * returning any kind of response at all.
     *
     * @param array $reports ['view' => 'data']
     * @param string $file
     * @return \Illuminate\Http\Response
     */
    function combine_reports_pdf_and_save($reports, $file)
    {
        $viewFactory = app()['view'];
        $reportContent = '';
        foreach ($reports as $template => $data) {
            $reportContent .= $viewFactory->make($template, $data)->render();
        }
        PDFFacade::loadHTML($reportContent)->save($file);

        return $file;
    }
}

if (!function_exists('combine_reports_doc')) {
    /**
     * Combine reports as word documents.
     *
     * @param array $reports ['view' => 'data']
     * @param string $file
     * @return \Illuminate\Http\Response
     */
    function combine_reports_doc($reports, $file)
    {
        $phpword = new PhpWord;

        $viewFactory = app()['view'];
        $reportContent = '';
        foreach ($reports as $template => $data) {
            $reportContent .= $viewFactory->make($template, $data)->render();
        }

        // Adding an empty Section to the document...
        $section = $phpword->addSection();
        // Adding Text element to the Section having font styled by default...
        @\PhpOffice\PhpWord\Shared\Html::addHtml($section, $reportContent, true);

        // Save the document
        $writer = IOFactory::createWriter($phpword, 'Word2007');
        $writer->save(public_path('/reports/generated/' . $file));

        return response()->download(public_path('/reports/generated/' . $file), $file);
    }
}
