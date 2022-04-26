<?php

if (!function_exists('system_log')) {
    /**
     * Log an action into the logging system
     *
     * @param array $arguments
     *
     * @return bool
     */
    function system_log($arguments)
    {
        $logger = app(\App\Logger\Logger::class);

        foreach ($arguments as $argument => $value) {
            $logger->$argument($value);
        }

        return $logger->log();
    }
}
