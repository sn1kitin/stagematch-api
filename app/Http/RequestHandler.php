<?php
namespace App\Http;

class RequestHandler
{
    private array $routes;
    private string $method;
    // bv skills/{id}
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

        // vacancies/5/applications -> even posities zijn resources, oneven posities zijn ids
        $parts = [];
        foreach ($segments as $i => $segment) {
            if ($i % 2 === 0) {
                $parts[] = $segment;
                continue;
            }

            if (!ctype_digit($segment) || (int) $segment < 1) {
                ApiResponse::error(ApiResponse::BAD_REQUEST, 'Het id moet een positief geheel getal zijn.');
            }

            $this->ids[] = (int) $segment;
            $parts[] = '{id}';
        }

        $this->key = implode('/', $parts);
    }

    public function handle(): void
    {
        $route = $this->routes[$this->method][$this->key] ?? null;

        if ($route === null) {
            ApiResponse::error(ApiResponse::NOT_FOUND, 'Deze route bestaat niet.');
        }

        [$class, $action] = $route;
        $controller = new $class();

        $controller->$action(...$this->ids);
    }
}
