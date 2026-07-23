<?php

/*
|--------------------------------------------------------------------------
| Flash Message Helper
|--------------------------------------------------------------------------
| Stores temporary messages in the session.
| The message is displayed once and then automatically removed.
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Store a flash message.
 *
 * Available types:
 * success
 * error
 * warning
 * info
 */
function setFlash($type, $message)
{
    $_SESSION["flash"] = [
        "type" => $type,
        "message" => $message
    ];
}

/**
 * Display the flash message.
 */
function showFlash()
{
    if (!isset($_SESSION["flash"])) {
        return;
    }

    $flash = $_SESSION["flash"];

    $color = "#000";

    switch ($flash["type"]) {

        case "success":
            $color = "#198754";
            break;

        case "error":
            $color = "#dc3545";
            break;

        case "warning":
            $color = "#fd7e14";
            break;

        case "info":
            $color = "#0d6efd";
            break;

    }

    echo "
    <div style='
        padding:12px;
        margin-bottom:15px;
        border:1px solid {$color};
        color:{$color};
        border-radius:5px;
        font-weight:bold;
        background:#f8f9fa;
    '>
        " . htmlspecialchars($flash["message"]) . "
    </div>
    ";

    unset($_SESSION["flash"]);
}

/**
 * Check if a flash message exists.
 */
function hasFlash()
{
    return isset($_SESSION["flash"]);
}

?>