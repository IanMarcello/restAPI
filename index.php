<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
    require __DIR__ . "/app/Controller/$class.php";
});

set_exception_handler("ErrorHandler::handleException");
set_error_handler("ErrorHandler::handleError");

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "OPTIONS") {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ($parts[1] != "api") {
    http_response_code(404);
    exit;
}

$id = $parts[2] ?? null;

$database = new Database("localhost", "trial_db", "root", "");

$gateway = new TrialGateway($database);

$controller = new TrialController($gateway);

$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
