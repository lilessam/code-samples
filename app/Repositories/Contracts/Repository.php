<?php

namespace App\Repositories\Contracts;

interface Repository
{
    /**
     * Get all entries.
     *
     * @return mixed
     */
    public function all();

    /**
     * Get an entry by it's ID
     *
     * @param int
     */
    public function get($id);

    /**
     * Saves an entry.
     *
     * @param array
     */
    public function store(array $data);

    /**
     * Updates an entry.
     *
     * @param int
     * @param array
     */
    public function update($id, array $data);

    /**
     * Deletes an entry.
     *
     * @param int
     */
    public function delete($id);
}
