<?php

namespace AppBundle\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface DataSetterInterface
{
    /**
     * Create data.
     *
     * @param Request $request Information for creating.
     * @return Response
     */
    public function create(Request $request);

    /**
     * Update data.
     *
     * @param integer $id      Item id which we need to update.
     * @param Request $request Information for updating.
     * @return Response
     */
    public function update($id, Request $request);

    /**
     * Delete data.
     *
     * @param integer $id Item id which we need to delete.
     * @return Response
     */
    public function delete($id);
}