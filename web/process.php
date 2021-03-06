<?php

header('Content-type: application/json');

function path(string $id): string
{
    return __DIR__ . '/n/' . $id . '.txt';
}

function url(string $id): string
{
    return ($_ENV['URL'] ?? '') . '/n/' . $id . '.txt';
}

if (isset($_POST['note']) && empty($_POST['note']) == false) {
    try {
        do {
            $id = str_replace(['0', 'o'], 'x', bin2hex(random_bytes(2)));
        } while (file_exists(path($id)));

        file_put_contents(path($id), $_POST['note']);

        http_response_code(200);
        exit(json_encode(['id' => $id, 'url' => url($id)]));
    } catch (Exception $e) {
        error_log(json_encode($e));
        http_response_code(500);
        exit(json_encode(['error' => 'Internal Error!']));
    }
} elseif (isset($_GET['id']) && empty($_GET['id']) == false) {
    $path = path(strtolower($_GET['id']));
    if (file_exists($path)) {
        http_response_code(200);
        exit(json_encode(['note' => file_get_contents($path)]));
    }

    http_response_code(404);
    exit(json_encode(['error' => 'Note Not Found!']));
} else {
    http_response_code(400);
    exit(json_encode(['error' => 'Bad Request!']));
}
