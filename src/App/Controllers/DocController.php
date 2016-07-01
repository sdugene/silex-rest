<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;


class DocController
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function routes()
    {
        return new JsonResponse($this->app['routes.list']);
    }
}
