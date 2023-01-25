<?php

header("Access-Control-Allow-Origin: *");

$method = $_SERVER['REQUEST_METHOD'];

$part = explode("/", $_SERVER['REQUEST_URI']);

echo json_encode($part);

if ($method == 'GET') {
    echo "THIS IS A GET REQUEST";
}
if ($method == 'POST') {
    echo "THIS IS A POST REQUEST";
}
