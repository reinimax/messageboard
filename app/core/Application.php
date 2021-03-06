<?php

namespace app\core;

class Application
{
    /**
     * A multi-dimensional associative array. The first key corresponds to the method, the second key corresponds to the path
     */
    protected $routes = [];
    protected $view;

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Get the route that was entered and the method and call the corresponding controller with the approriate method
     */
    public function run()
    {
        // get the method
        $method = $this->getMethod();
        // get the route
        $route = $this->getRoute();
        // call the approritate controller
        if (isset($this->routes[$method][$route])) {
            if (class_exists($this->routes[$method][$route][0])) {
                $controller = new $this->routes[$method][$route][0]();
                // call the method of the controller
                try {
                    $result = $controller->{$this->routes[$method][$route][1]}();
                    $this->view->render($result);
                } catch (\Error $e) {
                    $title = 'Error '.$e->getCode();
                    $message = $e->getMessage();
                    $this->view->render([
                        'title' => 'Error',
                        'content' => 'error.php',
                        'data' => [
                            'error' => $title,
                            'errormsg' => $message
                        ]
                    ]);
                }
            } else {
                // handle error
                $this->view->render([
                    'title' => 'Error',
                    'content' => 'error.php',
                    'data' => [
                        'error' => 'Controller not found',
                        'errormsg' => 'The controller for this route was not found'
                    ]
                ]);
            }
        } else {
            // handle error
            $this->view->render([
                'title' => 'Error',
                'content' => 'error.php',
                'data' => [
                    'error' => 'Method/Route not found',
                    'errormsg' => 'The method '.$method.' or the route '.$route.' was not found'
                ]
            ]);
        }
    }

    protected function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get the route
     * @return string The route without extension and query string
     */
    protected function getRoute()
    {
        $route = $_SERVER['REQUEST_URI'];
        $info = pathinfo($route);
        $path = $info['dirname'].'/'.$info['filename'];
        return str_replace('\\', '', $path);
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
