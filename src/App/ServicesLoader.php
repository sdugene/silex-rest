<?php

namespace App;

use Silex\Application;

class ServicesLoader
{
    protected $app;
    public $routes;

    public function __construct(Application $app, $routes)
    {
        $this->app = $app;
        $this->routes = $routes;
    }

    public function bindServicesIntoContainer()
    {
        foreach($this->routes as $key => $route) {
            $this->app[$route['tableName'].'.service'] = $this->app->share(function () use ($key, $route) {
                $serviceName = 'App\\Services\\'.$key.'Service';
                if (class_exists($serviceName)) {
                    return new $serviceName($this->app["db"], $route);
                } else {
                    return new Services\EntityService($this->app["db"], $route);
                }
            });
        }
    }
}

