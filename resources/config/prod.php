<?php
$app['log.level'] = Monolog\Logger::ERROR;
$app['api.version'] = "v1";
$app['api.endpoint'] = "/api";
$app['security.level'] = "key";
$app['db.options'] = [
    "driver"   => "pdo_mysql",
    "user"     => "*****",
    "password" => "*****",
    "dbname"   => "*****",
    "host"     => "*****",
    "charset" => 'utf8',
    "driverOptions" => array(
        1002=>'SET NAMES utf8'
    )
];
