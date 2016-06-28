<?php

define("ROOT", __DIR__ . '/../');
require_once ROOT . 'vendor/autoload.php';

$app = new Silex\Application();

require ROOT . 'resources/config/config.php';

require ROOT . 'src/app.php';

$app['http_cache']->run();
