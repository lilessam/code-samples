<?php

namespace App\Repositories;

use Closure;
use App\Models\Match;
use App\Repositories\Contracts\Repository;

class Matches extends Base implements Repository
{
    /**
     * Determines the model.
     *
     * @return mixed
     */
    public function model()
    {
        return Match::class;
    }

    /**
     * Get all entries.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->model()::all();
    }

    /**
     * Get an entry by it's ID
     *
     * @param int
     */
    public function get($id)
    {
        return $this->model()::find($id);
    }

    /**
     * Saves an entry.
     *
     * @param array
     * @return App\Models\Match
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
     * @return App\Models\Match
     */
    public function update($id, array $data)
    {
        $model = $this->model()::find($id);
        $model->fill($data);
        $model->save();
        return $model;
    }

    /**
     * Deletes entries.
     *
     * @param int|Closure
     */
    public function delete($identifier)
    {
        if (is_int($identifier)) {
            return $this->model()::destroy($identifier);
        }

        if (is_callable($identifier)) {
            return $this->model()::where(function($query) use ($identifier) {
                return $identifier($query);
            })->delete();
        }
    }

    /**
     * Following methods are not the implemented
     * interface.
     */

    /**
     * @param int $account_id
     * @param int $limit
     * @param string|null $keyword
     * @param string $driver
     * @return \Collection
     */
    public function paginate($account_id, $limit, $keyword = null, $driver = 'All')
    {
        $query = $this->model()::whereAccountId($account_id);
        if ($keyword) {
            $query->where(function($query) use($keyword) {
                return $query->where('name', 'LIKE', '%'.$keyword.'%')
                ->orWhere('city', 'LIKE', '%'.$keyword.'%')
                ->orWhere('state', 'LIKE', '%'.$keyword.'%');
            });
        }
        if ($driver && $driver != 'All') {
            $query->where('driver', strtoupper($driver));
        }
        return $query->paginate($limit);
    }
}
