<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class EntityController
{
    private $app;
    protected $service;
    protected $route;

    public function __construct($app, $service, $route)
    {
        $this->app = $app;
        $this->service = $service;
        $this->route = $route;
    }

    public function getById($id)
    {
        return new JsonResponse($this->service->getById($id));
    }

    public function getByIdWithJoin($id, $join)
    {
        return new JsonResponse($this->service->getByIdWithJoin($id, $join));
    }

    public function getAll()
    {
        return new JsonResponse($this->service->getAll());
    }

    public function getAllWithJoin($join)
    {
        return new JsonResponse($this->service->getAllWithJoin($join));
    }

    public function save(Request $request)
    {
        $values = $this->getDataFromRequest($request);
        return new JsonResponse(array("id" => $this->service->save($values)));
    }

    public function search(Request $request, $maxLine = false, $order = false, $group = false)
    {
        if (!is_null($request->get('criteria'))) {
            $criteria = $this->getDataFromArray($request->get('criteria'), true);
            $maxLine = $request->get('maxLine');
            $order = $request->get('order');
            $group = $request->get('group');
        } else {
            $criteria = $this->getDataFromRequest($request, true);
        }
        return new JsonResponse($this->service->search($criteria, $maxLine, $order, $group));
    }

    public function searchWithJoin(Request $request, $join)
    {
        if (!is_null($request->get('criteria'))) {
            $criteria = $this->getDataFromArray($request->get('criteria'), true);
            $maxLine = $request->get('maxLine');
            $order = $request->get('order');
            $group = $request->get('group');
        } else {
            $criteria = $this->getDataFromRequest($request, true);
        }
        return new JsonResponse($this->service->searchWithJoin($criteria, $join, $maxLine, $order, $group));
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

    public function getDataFromRequest($request, $addId = false)
    {
        return $this->getDataFromArray($request->request->all(), $addId);
    }

    private function getDataFromArray($array, $addId = false)
    {
        $values = [];
        foreach($array as $key => $value) {
            if (is_array($value)) {
                $values[$key] = $this->getDataFromArray($value, $addId);
            }
            if((in_array($key, $this->route['attributes'])) || ($addId && $key == $this->route['idColumn'])) {
                $values[$key] = $value;
            }
        }
        return $values;
    }
}
