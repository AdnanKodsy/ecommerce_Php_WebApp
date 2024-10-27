<?php
namespace SCANDIWEB;

class Router
{
    private $routes = [];

    public function register(string $route, callable $action, string $method): self
    {
        $this->routes[$route] = ['action' => $action, 'method' => strtoupper($method)];
        return $this;
    }

    public function resolve(string $requestUri, string $requestMethod)
    {
        foreach ($this->routes as $route => $details) {
            if ($route === $requestUri && $details['method'] === strtoupper($requestMethod)) {
                return call_user_func($details['action']);
            }
        }

        // Handle 404
        header("HTTP/1.0 404 Not Found");
        echo json_encode(["message" => "Not Found"]);
    }
}