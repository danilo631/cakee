<?php
function handleError($errno, $errstr, $errfile, $errline) {
    error_log("[Error] $errstr in $errfile on line $errline");
    http_response_code(500);
    die("Ocorreu um erro. Os administradores foram notificados.");
}

set_error_handler("handleError");

function handleException($exception) {
    error_log("[Exception] " . $exception->getMessage());
    http_response_code(500);
    die("Ocorreu um erro inesperado.");
}

set_exception_handler("handleException");

function handleShutdown() {
    $error = error_get_last();
    if ($error !== null) {
        error_log("[Shutdown] " . $error['message']);
        http_response_code(500);
        die("Ocorreu um erro durante o processamento.");
    }
}

register_shutdown_function("handleShutdown");
?>