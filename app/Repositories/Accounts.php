<?php

namespace App\Repositories;

use Closure;
use App\Models\Account;
use App\Repositories\Contracts\Repository;

class Accounts extends Base implements Repository
{
    /**
     * @var Matches
     */
    protected $matches;

    /**
     * Making a new instance of the repository.
     *
     * @param Matches $matches
     * @return void
     */
    public function __construct(Matches $matches)
    {
        $this->matches = $matches;
    }

    /**
     * Determines the model.
     *
     * @return mixed
     */
    public function model()
    {
        return Account::class;
    }

    /**
     * Get all entries.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model()::orderBy('name')->orderBy('state')->orderBy('city')->get();
    }

    /**
     * Get an entry by it's ID
     *
     * @param int
     * @return \App\Models\Account
     */
    public function get($id)
    {
        return $this->model()::find($id);
    }

    /**
     * Saves an entry.
     *
     * @param array
     * @return \App\Models\Account
     */
    public function store(array $data)
    {
        $model = $this->newInstance();
        $model->fill($data);
        $model->save();

        return $model;
    }

    /**
     * Updates an entry.
     *
     * @param int
     * @param array
     * @return \App\Models\Account
     */
    public function update($id, array $data)
    {
        $model = $this->model()::find($id);
        $model->fill($data);
        $model->save();

        foreach ($model->matches as $match) {
            if ($match->driver == 'All Manufacturers') {
                $newMatches = [];
                foreach (config('converter.drivers') as $driver) {
                    $newMatches[] = $this->matches->newInstance()->fill([
                        'driver' => $driver,
                        'name' => $match->name,
                        'city' => $match->city,
                        'state' => $match->state,
                        'zipcode' => $match->zipcode,
                    ]);
                }
                $this->saveMatches($model, $newMatches);
                $this->matches->delete($match->id);
            }
        }

        return $model;
    }

    /**
     * Deletes entries.
     *
     * @param int|Closure
     * @return int
     */
    public function delete($identifier)
    {
        if (is_int($identifier)) {
            $this->matches->delete(function ($query) use ($identifier) {
                return $query->whereAccountId($identifier);
            });

            return $this->model()::destroy($identifier);
        }

        if (is_callable($identifier)) {
            $records = $this->model()::where(function ($query) use ($identifier) {
                return $identifier($query);
            })->get();

            foreach ($records as $record) {
                $this->matches->delete(function ($query) use ($record) {
                    return $query->whereAccountId($record->id);
                });

                $record->delete();
            }
        }
    }

    /**
     * Following methods are not the implemented
     * interface.
     */

    /**
     * Save many entries at once.
     *
     * @param array $accounts
     * @return void
     */
    public function saveMany(array $accounts)
    {
        foreach ($accounts as $account) {
            $model = $this->store(collect($account)->except('matches')->toArray());
            $account = (array) $account;
            foreach ($account['matches'] as $match) {
                $this->matches->store(array_merge($match, ['account_id' => $model->id]));
            }
        }
    }

    /**
     * Save matches.
     *
     * @param \App\Models\Account $account
     * @param array $matches
     *
     * @return void
     */
    public function saveMatches($account, $matches)
    {
        $account->matches()->saveMany($matches);
    }
}
