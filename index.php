<?php
declare(strict_types = 1);
header("Content-type: application/json; charset=UTF-8");

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

//set_exception_handler("ErrorHandler::handleException");

$parts = explode("?", $_SERVER["REQUEST_URI"]);
$parts = explode("/", $parts[0]);

$url_service = $parts[3];

$id = $parts[4] ?? null;

switch ($url_service) {
    case "peliculas":
        $database = new Database("localhost", "cine", "root", "");
        $gateway = new PeliculasGateway($database);
        $controller = new PeliculasController($gateway);
        break;
    case "personas":
        $database = new Database("localhost", "cine", "root", "");
        $gateway = new PersonasGateway($database);
        $controller = new PersonasController($gateway);
        break;
    default:
        http_response_code(404);
        exit;
}


$controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $_GET);