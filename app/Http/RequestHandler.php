<?php
namespace App\Http;

class RequestHandler
{
    private array $routes;
    private string $method;
    private string $key = '';
    private array $ids = [];

    public function __construct(array $routes)
    {
        $this->routes = $routes;
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);

        $this->parseUrl();
    }

    private function parseUrl(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        $segments = array_values(array_filter(explode('/', strtolower($path)), fn($s) => $s !== ''));

        $parts = [];
        foreach ($segments as $segment) {
            if (!ctype_digit($segment)) {
                $parts[] = $segment;
                continue;
            }

            if ((int) $segment < 1) {
                ApiResponse::error(ApiResponse::BAD_REQUEST, 'Het id moet een positief geheel getal zijn.');
            }

            $this->ids[] = (int) $segment;
            $parts[] = '{id}';
        }

        $this->key = implode('/', $parts);
    }

    private function getBody(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $body = file_get_contents('php://input');

            if (trim($body) === '') {
                return [];
            }

            $data = json_decode($body, true);

            if (!is_array($data)) {
                ApiResponse::error(ApiResponse::BAD_REQUEST, 'De body bevat geen geldige JSON.');
            }

            return $data;
        }

        if ($this->method === 'POST') {
            return $_POST;
        }

        parse_str(file_get_contents('php://input'), $data);

        return $data;
    }

    private function allowedMethods(): array
    {
        $allowed = [];

        foreach ($this->routes as $method => $routes) {
            if (isset($routes[$this->key])) {
                $allowed[] = $method;
            }
        }

        return $allowed;
    }

    public function handle(): void
    {
        if ($this->method === 'OPTIONS') {
            ApiResponse::preflight();
        }

        $route = $this->routes[$this->method][$this->key] ?? null;

        if ($route === null) {
            $allowed = $this->allowedMethods();

            if (!empty($allowed)) {
                header('Allow: ' . implode(', ', $allowed));
                ApiResponse::error(ApiResponse::METHOD_NOT_ALLOWED, "De methode {$this->method} is niet toegestaan voor deze url.");
            }

            ApiResponse::error(ApiResponse::NOT_FOUND, 'Deze route bestaat niet.');
        }

        [$class, $action] = $route;
        $controller = new $class();

        $args = $this->ids;

        if (in_array($this->method, ['POST', 'PUT', 'PATCH'])) {
            $args[] = $this->getBody();
        }

        $controller->$action(...$args);
    }
}
