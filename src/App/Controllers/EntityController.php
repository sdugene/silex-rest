<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class EntityController
{

    protected $service;
    protected $route;

    public function __construct($service, $route)
    {
        $this->service = $service;
        $this->route = $route;
    }

    public function getAll()
    {
        return new JsonResponse($this->service->getAll());
    }

    public function save(Request $request)
    {

        $values = $this->getDataFromRequest($request);
        return new JsonResponse(array("id" => $this->service->save($values)));

    }

    public function update($id, Request $request)
    {
        $values = $this->getDataFromRequest($request);
        $this->service->update($id, $values);
        return new JsonResponse($values);

    }

    public function delete($id)
    {
        return new JsonResponse($this->service->delete($id));
    }

    public function getDataFromRequest(Request $request)
    {
        return array(
            $this->route['tableName'] => $request->request->get($this->route['tableName'])
        );
    }
}
