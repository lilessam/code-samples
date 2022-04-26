<?php

namespace Reports;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Support\Closure;
use Reports\Reports\Expenses\Expenses;
use Reports\Reports\CostSummary\CostSummary;
use Reports\Reports\TaskSummary\TaskSummary;
use Reports\Reports\StatusReport\StatusReport;

class Report
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The registered custom report creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The array of created "reports".
     *
     * @var array
     */
    protected $reports = [];

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a report instance.
     *
     * @param  string  $report
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function report($report)
    {
        if (is_null($report)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL report for [%s].',
                static::class
            ));
        }

        // If the given report has not been created before, we will create the instances
        // here and cache it so we can return it next time very quickly. If there is
        // already an report created by this name, we'll just return that instance.
        if (!isset($this->reports[$report])) {
            $this->reports[$report] = $this->createReport($report);
        }

        return $this->reports[$report];
    }

    /**
     * Create a new report instance.
     *
     * @param  string  $report
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createReport($report)
    {
        // First, we will determine if a custom report creator exists for the given report and
        // if it does not we will check for a creator method for the report. Custom creator
        // callbacks allow developers to build their own "reports" easily using Closures.
        if (isset($this->customCreators[$report])) {
            return $this->callCustomCreator($report);
        } else {
            $method = 'create' . Str::studly($report) . 'Report';

            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }
        throw new InvalidArgumentException("Report [$report] not supported.");
    }

    /**
     * Call a custom report creator.
     *
     * @param  string  $report
     * @return mixed
     */
    protected function callCustomCreator($report)
    {
        return $this->customCreators[$report]($this->app);
    }

    /**
     * Register a custom report creator Closure.
     *
     * @param  string    $report
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($report, Closure $callback)
    {
        $this->customCreators[$report] = $callback;

        return $this;
    }

    /**
     * Get all of the created "reports".
     *
     * @return array
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * Dynamically call the default report instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->report()->$method(...$parameters);
    }

    /**
     * Create cost summary report instance.
     *
     * @return \Reports\Reports\CostSummary
     */
    public function createCostSummaryReport()
    {
        return new CostSummary($this->app['view']);
    }

    /**
     * Create task summary report instance.
     *
     * @return \Reports\Reports\TaskSummary
     */
    public function createTaskSummaryReport()
    {
        return new TaskSummary($this->app['view']);
    }

    /**
     * Create expenses report instance.
     *
     * @return \Reports\Reports\Expenses
     */
    public function createExpensesReport()
    {
        return new Expenses($this->app['view']);
    }

    /**
     * Create status report instance.
     *
     * @return \Reports\Reports\StatusReport
     */
    public function createStatusReportReport()
    {
        return new StatusReport($this->app['view']);
    }
}
