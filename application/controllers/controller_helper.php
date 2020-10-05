<?php

define('PLAYER_UNREGISTERED', -1);
define('PLAYER_BANNED', -2);
define('SERVER_ERROR', 500);
define('OK_RESPONSE', 1);

function ok_response() {
    http_response_code(200);
    return OK_RESPONSE;
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

function internal_server_error(string $message = 'Internal Server Error') {
    http_response_code(500);
    return $message;
}

function banned() {
    return PLAYER_BANNED;
}

function player_unregistered() {
    return PLAYER_UNREGISTERED;
}

function unprocessed() {
    return 2;
}

function operation_ok($result = OK_RESPONSE, array $additionalData = []): array {
    return ['status' => true, 'result' => $result] + $additionalData;
}

function operation_failed(int $error_code, array $additionalData = []): array {
    return ['status' => false, 'error_code' => $error_code] + $additionalData;
}

function current_player_id(): ?int {
    return $_SESSION['player_id'];
}