<?php

if (!function_exists('versioning_get')) {
    /**
     * Get versions of an entity.
     *
     * @param string $entity
     * @param integer $entity_id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function versioning_get(string $entity, int $entity_id)
    {
        return config('versioning.model')::get($entity, $entity_id);
    }
}
