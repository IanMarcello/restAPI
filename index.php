<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
    require __DIR__ . "/app/Controller/$class.php";
});

set_exception_handler("ErrorHandler::handleException");

header("Content-Type: application/json; charset=UTF-8");

// $method = $_SERVER['REQUEST_METHOD'];

$parts = explode("/", $_SERVER['REQUEST_URI']);

if($parts[1] != "trial") {
    http_response_code(404);
    exit;
}

$id = $parts[2] ?? null;

$database = new Database("localhost", "trial_db", "root", "");

$gateway = new TrialGateway($database);

$controller = new TrialController($gateway);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);

//  if ($method == 'GET') {
//      echo "THIS IS A GET REQUEST<br/>";
//  }
//  if ($method == 'POST') {
//      echo "THIS IS A POST REQUEST<br/>";
//  }

