<?php

namespace App;

use Silex\Application;

class ServicesLoader
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bindServicesIntoContainer()
    {
        foreach($this->app['routes.list'] as $key => $route) {
            $this->app[$route['tableName'].'.service'] = $this->app->share(function () use ($key, $route) {
                $serviceName = 'App\\Services\\'.$key.'Service';
                if (class_exists($serviceName)) {
                    return new $serviceName($this->app, $this->app['db'], $route);
                } else {
                    return new Services\EntityService($this->app, $this->app['db'], $route);
                }
            });
        }
    }
}

