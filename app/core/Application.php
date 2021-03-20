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
                    $title = (PRODUCTION === false) ? 'Error '.$e->getCode() : null;
                    $message = (PRODUCTION === false) ? $e->getMessage() : null;
                    $content = (PRODUCTION === false) ? 'error.php' : null;
                    $this->view->render([
                        'title' => 'Error',
                        'content' => $content,
                        'data' => [
                            'error' => $title,
                            'errormsg' => $message
                        ]
                    ]);
                }
            } else {
                // handle error
                $title = (PRODUCTION === false) ? 'Controller not found' : null;
                $message = (PRODUCTION === false) ? 'The controller for this route was not found' : null;
                $content = (PRODUCTION === false) ? 'error.php' : null;
                $this->view->render([
                    'title' => 'Error',
                    'content' => $content,
                    'data' => [
                        'error' => $title,
                        'errormsg' => $message
                    ]
                ]);
            }
        } else {
            // handle error
            $title = (PRODUCTION === false) ? 'Method/Route not found' : null;
            $message = (PRODUCTION === false) ? 'The method '.$method.' or the route '.$route.' was not found' : null;
            $content = (PRODUCTION === false) ? 'error.php' : null;
            $this->view->render([
                'title' => 'Error',
                'content' => $content,
                'data' => [
                    'error' => $title,
                    'errormsg' => $message
                ]
            ]);
        }
    }

    protected function getMethod()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($method === 'post') {
            if (isset($_POST['_method'])) {
                if ($_POST['_method'] === 'put') {
                    $method = 'put';
                }
                if ($_POST['_method'] === 'delete') {
                    $method = 'delete';
                }
            }
        }
        return $method;
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
        return str_replace(['\\', '//'], ['', '/'], $path);
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
