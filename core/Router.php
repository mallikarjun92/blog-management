<?php

namespace Core;

use ReflectionClass;
use ReflectionMethod;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class Router
{
    protected $routes = [];

    public function __construct()
    {
        $this->scanControllers();
    }

    /**
     * Scan all controllers in the App\Controllers directory and register routes from annotations.
     */
    protected function scanControllers()
    {
        // Path to the controllers directory
        $controllersDir = dirname(__DIR__) . '\app\controllers';

        if (!is_dir($controllersDir)) {
            throw new \RuntimeException("Controllers directory not found: {$controllersDir}");
        }

        // Recursively scan all PHP files in the controllers directory
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllersDir));
        $phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);

        
        foreach ($phpFiles as $file) {
            
            require_once $file[0];
        }

        // Get all declared classes
        $classes = get_declared_classes();

        foreach ($classes as $className) {
            // Check if class is in the App\Controllers namespace and extends Core\Controller
            if (strpos($className, 'App\\Controllers\\') === 0 && is_subclass_of($className, 'Core\\BaseController')) {

                $this->processController($className);
            }
        }
    }

    /**
     * Process a controller class and register its routes.
     * 
     * @param string $className Fully qualified class name.
     */
    protected function processController($className)
    {
        $reflectionClass = new ReflectionClass($className);

        foreach ($reflectionClass->getMethods() as $method) {
            // Skip non-public methods and inherited methods
            if (!$method->isPublic() || $method->getDeclaringClass()->getName() !== $className) {
                continue;
            }

            $docComment = $method->getDocComment();
            if (!$docComment) {
                continue;
            }

            // Parse @Route annotations
            preg_match_all('/@Route\(([^)]+)\)/', $docComment, $matches);

            foreach ($matches[1] as $paramsStr) {
                $params = $this->parseAnnotationParams($paramsStr);

                if (!isset($params['path'])) {
                    continue; // Skip if path is missing
                }

                // Get HTTP methods (default to GET)
                $methods = isset($params['method']) ? 
                    explode(',', $params['method']) : 
                    ['GET'];
                $methods = array_map('strtoupper', $methods);

                // Get route name (optional)
                $name = $params['name'] ?? null;

                // Add the route
                $this->add(
                    $params['path'],
                    $reflectionClass->getShortName(), // Controller short name
                    $method->getName(),               // Action method name
                    $methods,
                    $name
                );
            }
        }
    }

    /**
     * Parse annotation parameters string into key-value pairs.
     * 
     * @param string $paramsStr The parameters string from the annotation.
     * @return array Parsed parameters.
     */
    protected function parseAnnotationParams($paramsStr)
    {
        $params = [];
        // Split parameters by commas not inside quotes
        $paramPairs = preg_split('/,(?=(?:[^"]*"[^"]*")*[^"]*$)/', $paramsStr);

        foreach ($paramPairs as $pair) {
            $pair = trim($pair);
            if (empty($pair)) continue;

            list($key, $value) = explode('=', $pair, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'"); // Trim quotes and whitespace

            $params[$key] = $value;
        }

        return $params;
    }

    /**
     * Add a new route to the routing table.
     * 
     * @param string $route The route URL.
     * @param string $controller The controller class name (without namespace).
     * @param string $action The action method name.
     * @param array $methods Supported HTTP methods.
     * @param string|null $name Route name (optional).
     */
    public function add($route, $controller, $action, $methods = ['GET'], $name = null)
    {
        $this->routes[$route] = [
            'controller' => $controller,
            'action' => $action,
            'methods' => array_map('strtoupper', $methods),
            'name' => $name
        ];
    }

    public function dispatch($url)
    {
        $url = ($url != '/') ? rtrim($url, '/') : $url;
        $request = new Request();
        
        foreach ($this->routes as $routeUrl => $route) {
            $pattern = preg_replace('/{(\w+)}/', '([^/]+)', $routeUrl);
            $pattern = "#^{$pattern}$#";            

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remove full match

                $method = $_SERVER['REQUEST_METHOD'];

                if (in_array($method, $route['methods'])) {
                    $controllerName = 'App\\Controllers\\' . $route['controller'];
                    $actionName = $route['action'];

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();

                        if (method_exists($controller, $actionName)) {
                            call_user_func_array(
                                [$controller, $actionName], 
                                array_merge([$request], $matches)
                            );
                            return;
                        } else {
                            $this->sendNotFound("Action {$actionName} not found.");
                        }
                    } else {
                        $this->sendNotFound("Controller {$controllerName} not found.");
                    }
                } else {
                    $this->sendMethodNotAllowed($route['methods']);
                }
            }
        }
        
        $this->sendNotFound("Route {$url} not found.");
    }

    // ... (keep existing sendNotFound and sendMethodNotAllowed methods)

    /**
     * Get route path by name.
     * 
     * @param string $name Route name.
     * @return string|null Route path or null if not found.
     */
    public function getRouteByName($name)
    {
        foreach ($this->routes as $path => $config) {
            if (isset($config['name']) && $config['name'] === $name) {
                return $path;
            }
        }
        return null;
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
