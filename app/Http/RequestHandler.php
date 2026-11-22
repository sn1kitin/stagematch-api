<?php
namespace App\Http;

class RequestHandler
{
    private array $routes;
    private string $method;
    // bv vacancies/{id}/applications
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

        // projectmap eraf halen, bv /stagematch-api
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        $segments = array_values(array_filter(explode('/', strtolower($path)), fn($s) => $s !== ''));

        // een getal is een id, de rest is een resource. Om en om werkt niet meer door me/skills/3
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

    // json body of gewone formdata
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

        // bij PUT en PATCH vult php $_POST niet, dus zelf de body uitlezen
        parse_str(file_get_contents('php://input'), $data);

        return $data;
    }

    public function handle(): void
    {
        $route = $this->routes[$this->method][$this->key] ?? null;

        if ($route === null) {
            ApiResponse::error(ApiResponse::NOT_FOUND, 'Deze route bestaat niet.');
        }

        [$class, $action] = $route;
        $controller = new $class();

        // eerst de ids uit de url, daarna de body
        $args = $this->ids;

        if (in_array($this->method, ['POST', 'PUT', 'PATCH'])) {
            $args[] = $this->getBody();
        }

        $controller->$action(...$args);
    }
}
