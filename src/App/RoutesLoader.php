<?php

namespace App;

use Silex\Application;

class RoutesLoader
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->instantiateControllers();
    }

    private function instantiateControllers()
    {
        foreach($this->app['routes.list'] as $route) {
            $this->app[$route['tableName'].'.controller'] = $this->app->share(function () use ($route) {
                return new Controllers\EntityController($this->app, $this->app[$route['tableName'].'.service'], $route);
            });
        }
        $this->app['doc.controller'] = $this->app->share(function () {
            return new Controllers\DocController($this->app);
        });
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app['controllers_factory'];
        $pattern = '/'.$this->app['api.version'].'\/([^\/]*)(?:(?:[\/\d]+)|(?:\/search)|(?:(?:(?:[\/\d]+)|(?:\/search)\/)?([^\/]*)))?$/';
        preg_match($pattern, $_SERVER['REQUEST_URI'], $matches);

        if (empty($this->app['authorized.methods'])) {
            return false;
        }

        foreach($this->app['routes.list'] as $route) {
            if ($route['tableName'] == $matches[1]){
                $api->get('/' . $route['tableName'], $route['tableName'] . '.controller:' . $route['methods']['getAll']);
                $api->get('/' . $route['tableName'] . '/{id}', $route['tableName'] . '.controller:' . $route['methods']['get'])
                    ->assert('id', '\d+');
                $api->post('/' . $route['tableName'] . '/search', $route['tableName'] . '.controller:' . $route['methods']['search']);

                if(array_key_exists(2, $matches) && array_key_exists($matches[2], $route['foreignKeys'])) {
                    $api->get('/' . $route['tableName'] . '/{join}', $route['tableName'] . '.controller:' . $route['methods']['getAllWithJoin']);
                    $api->get('/' . $route['tableName'] . '/{id}' . '/{join}', $route['tableName'] . '.controller:' . $route['methods']['getWithJoin'])
                        ->assert('id', '\d+');
                    $api->post('/' . $route['tableName'] . '/search' . '/{join}', $route['tableName'] . '.controller:' . $route['methods']['searchWithJoin']);
                }

                if (array_key_exists('post', $route['methods']) && in_array('post', $this->app['authorized.methods'])) {
                    $api->post('/' . $route['tableName'], $route['tableName'] . '.controller:' . $route['methods']['post']);
                }

                if (array_key_exists('put', $route['methods']) && in_array('put', $this->app['authorized.methods'])) {
                    $api->put('/' . $route['tableName'] . '/{id}', $route['tableName'] . '.controller:' . $route['methods']['put']);
                }

                if (array_key_exists('delete', $route['methods']) && in_array('delete', $this->app['authorized.methods'])) {
                    $api->delete('/' . $route['tableName'] . '/{id}', $route['tableName'] . '.controller:' . $route['methods']['delete']);
                }
                break;
            }
        }
        $api->get('/doc/routes', 'doc.controller:routes');

        $this->app->mount($this->app['api.endpoint'].'/'.$this->app['api.version'], $api);
    }
}

