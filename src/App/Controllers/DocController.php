<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;


class DocController
{
    private $app;
    private $route = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function routes()
    {
        return new JsonResponse($this->app['routes.list']);
    }

    public function route($route)
    {
        return new JsonResponse($this->getRoute($route));
    }


    private function getRoute($name)
    {
        if (empty($this->route)) {
            $this->route = [];
            foreach($this->app['routes.list'] as $route) {
                if ($route['tableName'] == $name) {
                    $this->route = $route;
                    break;
                }
            }
        }
        return $this->route;
    }
}
