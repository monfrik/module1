<?php
class Bootstrap
{
    private $route = [
        'GET' => 'GetRoute',
        'POST' => 'PostRoute',
        'DELETE' => 'DeleteRoute'
    ];
    public function __construct(){
        $route = new $this->route[$_SERVER['REQUEST_METHOD']];
    }
}