<?php

if (!function_exists('service')) {
    /**
     * Initialize a service.
     * @param string $service
     * @return void
     */
    function service($service) {
        $serviceClass = 'App\\Services\\' . ucfirst($service);
        return (new $serviceClass);
    }
}