<?php
$app['log.level'] = Monolog\Logger::ERROR;
$app['api.version'] = "v1";
$app['api.endpoint'] = "/api";
$app['db.options'] = [
    "driver"   => "pdo_mysql",
    "user"     => "root",
    "password" => "root",
    "dbname"   => "dev_db",
    "host"     => "localhost"
];