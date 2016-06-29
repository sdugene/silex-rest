<?php

namespace App;

use Silex\Application;

class RoutesLoader
{
    private $app;
    public $routes;

    public function __construct(Application $app, $routes)
    {
        $this->app = $app;
        $this->routes = $routes;
        $this->instantiateControllers();
    }

    private function instantiateControllers()
    {
        foreach($this->routes as $route) {
            $this->app[$route['tableName'].'.controller'] = $this->app->share(function () use ($route) {
                return new Controllers\EntityController($this->app[$route['tableName'].'.service'], $route);
            });
        }

    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        foreach($this->routes as $route) {
            $api->get('/'.$route['tableName'], $route['tableName'].'.controller:'.$route['methods']['getAll']);
            $api->get('/'.$route['tableName'].'/{id}', $route['tableName'].'.controller:'.$route['methods']['get']);

            if (array_key_exists('post', $route['methods']['post'])) {
                $api->post('/'.$route['tableName'], $route['tableName'].'.controller:'.$route['methods']['post']);
            }

            if (array_key_exists('put', $route['methods']['post'])) {
                $api->put('/'.$route['tableName'].'/{id}', $route['tableName'].'.controller:'.$route['methods']['put']);
            }

            if (array_key_exists('delete', $route['methods']['post'])) {
                $api->delete('/'.$route['tableName'].'/{id}', $route['tableName'].'.controller:'.$route['methods']['delete']);
            }
        }

        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }
}

