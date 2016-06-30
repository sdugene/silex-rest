<?php
$app['log.level'] = Monolog\Logger::DEBUG;
$app['api.version'] = "v1";
$app['api.endpoint'] = "/api";
$app['security.level'] = "all";
$app['db.options'] = [
    "driver"   => "pdo_mysql",
    "user"     => "root",
    "password" => "root",
    "dbname"   => "dev_db",
    "host"     => "localhost",
    "charset" => 'utf8',
    "driverOptions" => array(
        1002=>'SET NAMES utf8'
    )
];