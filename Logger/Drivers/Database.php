<?php

namespace App\Logger\Drivers;

use App\Models\System\Log;
use App\Logger\Contracts\Driver;

class Database implements Driver
{
    /**
     * The model ID that made the action.
     *
     * @var int
     */
    public $user_id;

    /**
     * The model who took the action.
     *
     * @var string
     */
    public $model;

    /**
     * The action that has been done. Should be from config array.
     *
     * @var string
     */
    public $action = null;

    /**
     * The description of the action.
     *
     * @var string
     */
    public $description = null;

    /**
     * The model that the action has been taked on.
     *
     * @var string|ull
     */
    public $entity = null;

    /**
     * The entity ID of the action.
     *
     * @var int|null
     */
    public $entity_id = null;

    /**
     * Call unknown function or set the object attributes
     *
     * @param string $method
     * @param string $arguments
     * @return void
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            $this->$method(...$arguments);
        } else {
            $this->$method = $arguments[0];
        }

        return $this;
    }

    /**
     * Get the user ID of the action.
     *
     * @return int
     */
    public function getUserID()
    {
        return $this->user_id;
    }

    /**
     * Get the model who took the action.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model ?: config('logger.model');
    }

    /**
     * Get the instance of the model using user ID.
     *
     * @return mixed
     */
    public function getModelInstance()
    {
        return $this->getModel()::find($this->user_id);
    }

    /**
     * Get the action type.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get the description of the action.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the entity/model of the action.
     *
     * @return string|null
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get the Entity ID.
     *
     * @return int|null
     */
    public function getEntityID()
    {
        return $this->entity_id;
    }

    /**
     * Save the log in the driver.
     *
     * @return void
     */
    public function log() : bool
    {
        $this->description = $this->generateDescription();

        $log = new Log;
        $log->user_id = $this->user_id;
        $log->description = $this->description;
        $log->entity = $this->entity;
        $log->entity_id = $this->entity_id;

        return $log->save() ? true : false;
    }

    /**
     * Generate a description based on current available
     * attributes.
     *
     * @return string
     */
    public function generateDescription()
    {
        $description = $this->getModelInstance()->getName() . ' has ' . config('logger.actions')[$this->action];

        if ($this->entity && $this->entity_id) {
            $description .= ' a ' . $this->entity;
        }

        return $description;
    }
}
