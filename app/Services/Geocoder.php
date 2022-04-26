<?php

namespace App\Services;

use App\Models\Address;
use App\Services\Contracts\Service;

class Geocoder extends BaseService implements Service
{
    /**
     * @var string
     */
    private $baseEndpoint = 'https://maps.googleapis.com/maps/api/geocode/json?';

    /**
     * @var string
     */
    private $apiKey = 'AIzaSyAWCv7lDN7P9sBD6uxXbyCGv9j1pf6vnow';

    /**
     * @var string
     */
    public $city = null;

    /**
     * @var string
     */
    public $state = null;

    /**
     * @var string
     */
    public $zip = null;

    /**
     * Set fallback values.
     *
     * @return self
     */
    private function errorFallback($city = null, $state = null)
    {
        $this->city = $city;
        $this->state = $state;

        return $this;
    }

    /**
     * @return void
     */
    public function process()
    {
        if (!$this->address) {
            return $this->errorFallback();
        }

        if (str_contains(strtolower($this->address), strtolower('will call'))) {
            return $this->errorFallback('WILL CALL');
        }

        $this->address = str_replace('E_x000D_', '', $this->address);
        $this->address = str_replace('_x000D_', '', $this->address);
        $this->address = str_replace('\n', '', $this->address);

        $addressFound = Address::whereAddress($this->address)->first();
        if ($addressFound) {
            $this->city = $addressFound->city;
            $this->state = $addressFound->state;
            $this->zip = $addressFound->zip;

            return $this;
        }
        $json = file_get_contents($this->baseEndpoint . 'address=' . urlencode($this->address) . '&key=' . $this->apiKey);
        $json = (array) json_decode($json);

        if (count($json['results']) == 0) {
            return $this->errorFallback();
        }

        $this->address_components = collect($json['results'][0]->address_components);
        $this->geometry = collect($json['results'][0]->geometry);

        $this->getCity();
        $this->getState();
        $this->getZip();

        if (!$this->city && !$this->state) {
            return $this->errorFallback();
        }

        $address = new Address;
        $address->fill([
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip
        ]);
        $address->save();

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        $cityFound = $this->address_components->first(function ($component) {
            return in_array('locality', $component->types) || in_array('sublocality', $component->types) || in_array('sublocality_level_1', $component->types) || in_array('administrative_area_level_3', $component->types);
        });

        return $cityFound ? $this->city = $cityFound->short_name : $this->city = null;
    }

    /**
     * @return string
     */
    public function getState()
    {
        $stateFound = $this->address_components->first(function ($component) {
            return in_array('administrative_area_level_1', $component->types);
        });

        return $stateFound ? $this->state = $stateFound->short_name : $this->state = null;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        $zipFound = $this->address_components->first(function ($component) {
            return in_array('postal_code', $component->types);
        });

        /**
         * If the zip code is not found, let's try to pick it from the coordinates
         * by making another request to the Geocoding API.
         */
        if (!$zipFound) {
            $latLng = $this->geometry['location']->lat . ',' . $this->geometry['location']->lng;
            $json = file_get_contents($this->baseEndpoint . 'latlng=' . $latLng . '&key=' . $this->apiKey);
            $json = (array) json_decode($json);

            if (count($json['results']) == 0) {
                return $this->zip = null;
            }

            $address_components = collect($json['results'][0]->address_components);

            $zipFound = $address_components->first(function ($component) {
                return in_array('postal_code', $component->types);
            });
        }

        return $zipFound ? $this->zip = $zipFound->short_name : $this->zip = null;
    }
}
