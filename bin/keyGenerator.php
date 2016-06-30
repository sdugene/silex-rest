<?php
define("ROOT", __DIR__ . '/../');

if (count($argv) > 1 && validateIp($argv[1]) || in_array($argv[1], ['all', 'get', 'post', 'put', 'delete'])) {
    $key = hash('sha256', uniqid (time(), true));
    $value = [
        'ip' => '',
        'methods' => []
    ];

    if (validateIp($argv[1])) {
        $value['ip'] = $argv[1];
    } elseif ($argv[1] == 'all') {
        $value['methods'] = ['get', 'post', 'put', 'delete'];
    }

    foreach ($argv as $line => $method) {
        if ($line > 1 && !in_array($method, $value['methods']) && in_array($method, ['all', 'get', 'post', 'put', 'delete'])) {
            if ($method == 'all') {
                $value['methods'] = ['get', 'post', 'put', 'delete'];
            } else {
                $value['methods'][] = $method;
            }
        }
    }

    if (empty($value['methods'])) {
        echo 'Creation ERROR : no methods defined
';
    } elseif (file_put_contents(ROOT . 'storage/keys/'.$key.'.key', json_encode($value))) {
        chown(ROOT . 'storage/keys/'.$key.'.key', 'www-data');
        chgrp(ROOT . 'storage/keys/'.$key.'.key', 'www-data');
        echo 'Key creation done.
your key : '.$key.'
';
    } else {
        echo 'Creation ERROR : I can\'t write in folder '.ROOT . 'storage/keys/
';
    }

} else {
    echo 'please add params :
    keyGenerator all
    keyGenerator get
    keyGenerator get post
    keyGenerator get post put
    keyGenerator get post put delete
    keyGenerator [mixed value $ip] all
    keyGenerator [mixed value $ip] get
    keyGenerator [mixed value $ip] get post
    keyGenerator [mixed value $ip] get post put
    keyGenerator [mixed value $ip] get post put delete
';
}

function validateIp($ip)
{
    if (!filter_var($ip, FILTER_VALIDATE_IP) === false) {
        return true;
    } else {
        return false;
    }
}