<?php

namespace App;

use Silex\Application;

class SecurityKeysLoader
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->setSecurityLevels();
    }

    private function setSecurityLevels()
    {
        if ($this->app['security.level'] == 'all') {
            $this->app['authorized.methods'] = ['get','post','put','delete'];
        } elseif ($this->app['security.level'] == 'key') {
            if (file_exists(ROOT . 'storage/keys/'.$this->getHeader().'.key')) {
                $key = json_decode(file_get_contents(ROOT . 'storage/keys/'.$this->getHeader().'.key'), true) ;
                $this->app['authorized.methods'] = $this->checkKeys($key);
            } else {
                $this->app['authorized.methods'] = [];
            }
        } else {
            $this->app['authorized.methods'] = [];
        }
    }

    private function checkKeys($key)
    {
        if ($this->validateIp($key['ip']) && $key['ip'] == $this->getClientIp()) {
            return $key['methods'];
        } elseif ($key['ip'] == '' && $this->validateIp($this->getClientIp())) {
            $key['ip'] = $this->getClientIp();
            if (file_put_contents(ROOT . 'storage/keys/'.$this->getHeader().'.key', json_encode($key))) {
                return $key['methods'];
            }
        }
        return [];
    }

    private function getHeader($name = 'key')
    {
        return $_SERVER['HTTP_'.strtoupper($name)];
    }

    private function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validateIp($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($iplist as $ip) {
                    if ($this->validateIp($ip))
                        return $ip;
                }
            } else {
                if ($this->validateIp($_SERVER['HTTP_X_FORWARDED_FOR']))
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validateIp($_SERVER['HTTP_X_FORWARDED']))
            return $_SERVER['HTTP_X_FORWARDED'];
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validateIp($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validateIp($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validateIp($_SERVER['HTTP_FORWARDED']))
            return $_SERVER['HTTP_FORWARDED'];

        return $_SERVER['REMOTE_ADDR'];
    }

    private function validateIp($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return true;
        } else {
            return false;
        }
    }
}

