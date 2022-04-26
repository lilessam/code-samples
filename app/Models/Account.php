<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'city',
        'state'
    ];

    /**
     * @return mixed
     */
    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    /**
     * Find the account that matches the given data.
     *
     * @param string $driver
     * @param string $name
     * @param string $city
     * @param string $state
     * @param string $zipcode
     *
     * @return \App\Models\Account
     */
    public static function match($driver, $name, $city = null, $state = null, $zipcode = null)
    {
        $searchable = [];

        /**
         * Place all arguments in an array.
         */
        $ref = new \ReflectionMethod(__CLASS__, __FUNCTION__);
        foreach ($ref->getParameters() as $parameter) {
            $paramName = $parameter->getName();
            if (${$paramName} != null) {
                $searchable[$paramName] = trim(${$paramName});
            }
        }

        /**
         * Initial search must match the driver and the name.
         * BUT if the driver has an exception, We'll need to match
         * other mandatory fields as well.
         */
        $matches = Match::with('account')->whereDriver($searchable['driver'])->whereRaw('LOWER(name) LIKE (?)', ["%{$searchable['name']}%"]);

        $driverClass = 'App\Converter\Drivers\\' . strtoupper($searchable['driver']);

        if (isset($driverClass::$mandatoryMatch)) {
            foreach ($driverClass::$mandatoryMatch as $field) {
                if (key_exists($field, $searchable)) {
                    $matches = $matches->where($field, $searchable[$field]);
                }
            }
        }

        $matches = $matches->get();

        /**
         * Search the results and sort them by the records
         * that match the most.
         */
        $sorted = $matches->sortByDesc(function ($model) use ($searchable) {
            $num_of_matches = 0;
            foreach ($searchable as $key => $value) {
                if (strtolower($model->$key) == strtolower($value)) {
                    $num_of_matches += 1;
                }
            }

            return $num_of_matches;
        })->unique();

        return $sorted->first() ? $sorted->first()->account : null;
    }
}
