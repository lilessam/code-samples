<?php

namespace App\Logger\Contracts;

interface Driver
{
    /**
     * Get the user ID of the action.
     *
     * @return int
     */
    public function getUserID();

    /**
     * Get the model who took the action.
     *
     * @return string
     */
    public function getModel();

    /**
     * Get the instance of the model using user ID.
     *
     * @return mixed
     */
    public function getModelInstance();

    /**
     * Get the action type.
     *
     * @return string|null
     */
    public function getAction();

    /**
     * Get the description of the action.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get the entity/model of the action.
     *
     * @return string|null
     */
    public function getEntity();

    /**
     * Get the Entity ID.
     *
     * @return int|null
     */
    public function getEntityID();

    /**
     * Save the log in the driver.
     *
     * @return bool
     */
    public function log() : bool;

    /**
     * Generate a description based on current available
     * attributes.
     *
     * @return string
     */
    public function generateDescription();
}
