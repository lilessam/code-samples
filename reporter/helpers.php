<?php

use Reporter\Generator;

if (!function_exists('download_report')) {
    /**
     * Generates and returns a binary file of the report.
     *
     * @param  string  $file_name
     * @param  string  $template_path
     * @param  mixed  $data
     * @param  string|null  $driver
     * @return void
     */
    function download_report($file_name, $template_path, $data, $driver = null)
    {
        $generator = app(Generator::class);

        if ($driver == null) {
            return $generator->template($template_path)->name($file_name)->data($data)->download();
        } else {
            return $generator->driver($driver)->template($template_path)->name($file_name)->data($data)->download();
        }
    }
}

if (!function_exists('stream_report')) {
    /**
     * Generates and streams a binary file of the report.
     * SUPPORTS PDF ONLY NOW.
     * @param  string  $file_name
     * @param  string  $template_path
     * @param  mixed  $data
     * @param  string|null  $driver
     * @return void
     */
    function stream_report($file_name, $template_path, $data, $driver = null)
    {
        $generator = app(Generator::class);

        if ($driver == null) {
            return $generator->template($template_path)->name($file_name)->data($data)->generate()->stream();
        } else {
            return $generator->driver($driver)->template($template_path)->name($file_name)->data($data)->generate()->stream();
        }
    }
}
