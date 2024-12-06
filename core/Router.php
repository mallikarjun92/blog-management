<?php

namespace Core;

class Router
{
    protected $routes = [];

    /**
     * Add a new route to the routing table.
     * 
     * @param string $route The route URL.
     * @param string $controller The controller to handle the route.
     * @param string $action The action method of the controller.
     * @param array $methods HTTP methods supported by this route (default is ['GET']).
     */
    public function add($route, $controller, $action, $methods = ['GET'])
    {
        $this->routes[$route] = [
            'controller' => $controller,
            'action' => $action,
            'methods' => array_map('strtoupper', $methods)
        ];
    }

    public function dispatch($url)
    {
        $url = rtrim($url);  // Remove trailing slashes

        // Match routes with dynamic parameters
        foreach ($this->routes as $routeUrl => $route) {
            // Convert route pattern to regex
            $pattern = preg_replace('/{(\w+)}/', '([^/]+)', $routeUrl);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $url, $matches)) {
                // Extract dynamic parameters from the URL
                array_shift($matches); // Remove the full match

                $method = $_SERVER['REQUEST_METHOD'];

                // Check if the request method matches
                if (in_array($method, $route['methods'])) {
                    $controllerName = 'App\\Controllers\\' . $route['controller'];
                    $actionName = $route['action'];

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();

                        // Pass dynamic parameters to the controller action
                        if (method_exists($controller, $actionName)) {
                            call_user_func_array([$controller, $actionName], $matches);
                        } else {
                            $this->sendNotFound("Action {$actionName} not found.");
                        }
                    } else {
                        $this->sendNotFound("Controller {$controllerName} not found.");
                    }
                } else {
                    $this->sendMethodNotAllowed($route['methods']);
                }
                return;
            }
        }

        $this->sendNotFound("Route {$url} not found.");
    }

    private function sendNotFound($message)
    {
        http_response_code(404);
        echo "404 - Not Found: {$message}";
        exit();
    }

    private function sendMethodNotAllowed($allowedMethods)
    {
        http_response_code(405);
        echo "405 - Method Not Allowed. Allowed methods: " . implode(', ', $allowedMethods);
        exit();
    }
}
