<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class EntityController
{

    protected $service;
    protected $route;

    public function __construct($service, $route)
    {
        $this->service = $service;
        $this->route = $route;
    }

    public function getById($id)
    {
        return new JsonResponse($this->service->getById($id));
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
        $result = $this->service->update($id, $values);

        return new JsonResponse($result);

    }

    public function delete($id)
    {
        return new JsonResponse($this->service->delete($id));
    }

    public function getDataFromRequest(Request $request)
    {
        $values = [];
        foreach($request->request->all() as $key => $value) {
            if(in_array($key, $this->route['attributes'])) {
                $values[$key] = $value;
            }
        }
        return $values;
    }
}
