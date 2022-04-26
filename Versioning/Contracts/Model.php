<?php

namespace App\Versioning\Contracts;

interface Model
{
    /**
     * Get versions of an entity.
     *
     * @param string $entity
     * @param integer $entity_id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function get(string $entity, int $entity_id);

    /**
     * Add a version for an entity.
     *
     * @param string $entity
     * @param integer $entity_id
     * @param array $data
     *
     * @return bool
     */
    public static function add(string $entity, int $entity_id, array $data);
}
