<?php

namespace AppBundle\Interfaces;

interface DataStoreInterface
{
    /**
     * Get single item.
     *
     * @param integer $id Item id which we need to take.
     * @return string
     */
    public function get(int $id);

    /**
     * Get all items.
     *
     * @return string
     */
    public function getAll();
}
