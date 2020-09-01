<?php

function ok_response() {
    http_response_code(200);
    return 'ok';
}

function not_found() {
    http_response_code(404);
}

function bad_request() {
    http_response_code(400);
}

function unauthorized() {
    http_response_code(401);
    return 'You do not have the right privileges';
}

function forbidden() {
    http_response_code(403);
}

function method_not_allowed() {
    http_response_code(405);
}

function internal_server_error(string $message) {
    http_response_code(500);
    return $message;
}

function banned() {
    return -2;
}

function player_unregistered() {
    return -1;
}

function unprocessed() {
    return 2;
}