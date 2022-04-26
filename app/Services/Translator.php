<?php

namespace App\Services;

use App\Models\Account;
use App\Services\Contracts\Service;

class Translator extends BaseService implements Service
{
    /**
     * @return self
     */
    public function process()
    {
        /**
         * Assuming there's the needed values in the current object.
         * $attributes wil carry the data. ($var => $val)
         */

        $params_needed = [
            'driver' => null,
            'name' => null,
            'city' => null,
            'state' => null,
            'zipcode' => null,
        ];

        $attributes = array_merge($params_needed, $this->attributes);

        $account = Account::match(...array_values($attributes));

        /**
         * An account has been found.
         */
        if ($account) {
            $this->account = $account;
            /**
             * Override if the provided address have 'Will Call'
             */
            if (key_exists('address', $this->attributes) && $this->address != null && str_contains(strtolower(trim($this->address)), strtolower('will call'))) {
                $city = 'Will Call';
                $state = '';
            } else {
                /**
                 * Set the city and state normally.
                 * If the account doesn't have city or state, we'll use the provided ones from the driver.
                 */
                $city = config('converter.callbacks.city')($account->city) ?: config('converter.callbacks.city')($this->city);
                $state = $account->state ? trim($account->state) : trim($this->state);
            }
            $this->output = (object) [
                'name' => config('converter.callbacks.customer_name')($account->name),
                'city' => $city,
                'state' => $state
            ];
        } else {
            /**
             * Just format the provided date because there's no match
             */
            $this->output = (object) [
                'name' => config('converter.callbacks.customer_name')($this->name),
                'city' => $this->city ? config('converter.callbacks.city')($this->city) : null,
                'state' => $this->state ? trim($this->state) : null
            ];
        }

        return $this;
    }
}
