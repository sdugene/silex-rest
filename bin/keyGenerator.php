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

    foreach ($argv as $key => $value) {
        if ($key > 1 && !in_array($value, $value['methods'] && in_array($value, ['get', 'post', 'put', 'delete']))) {
            $value['methods'][] = $value;
        }
    }



    if (!empty($value['methods']) && file_put_contents(ROOT . 'storage/keys/'.$key.'.key', json_encode($value))) {
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