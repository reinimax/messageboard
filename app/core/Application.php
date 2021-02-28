<?php

namespace app\core;

class Application
{
    /**
     * A multi-dimensional associative array. The first key corresponds to the method, the second key corresponds to the path
     */
    protected $routes = [];

    public function __construct()
    {
        echo 'Application constructed';
    }

    /**
     * Register a route via the GET method
     * @param string $path The relative path, e.g. '/edit'
     * @param array $controllerInfo An array containing the location of the controller class and the method that should be called
     */
    public function registerGet(string $path, array $controllerInfo)
    {
        $this->routes['get'][$path] = $controllerInfo;
    }

    /**
     * Register a route via the POST method
     * @param string $path The relative path, e.g. '/edit'
     * @param array $controllerInfo An array containing the location of the controller class and the method that should be called
     */
    public function registerPost(string $path, array $controllerInfo)
    {
        $this->routes['post'][$path] = $controllerInfo;
    }

    /**
     * Register a route via the PUT method
     * @param string $path The relative path, e.g. '/edit'
     * @param array $controllerInfo An array containing the location of the controller class and the method that should be called
     */
    public function registerPut(string $path, array $controllerInfo)
    {
        $this->routes['put'][$path] = $controllerInfo;
    }

    /**
     * Register a route via the DELETE method
     * @param string $path The relative path, e.g. '/edit'
     * @param array $controllerInfo An array containing the location of the controller class and the method that should be called
     */
    public function registerDelete(string $path, array $controllerInfo)
    {
        $this->routes['delete'][$path] = $controllerInfo;
    }
}
