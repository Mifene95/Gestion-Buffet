<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function validar_acceso($roles_permitidos)
{
    if (!isset($_SESSION["user_id"]) || !in_array($_SESSION["role_id"], $roles_permitidos)) {
        header("Location: ../index.php");
        exit();
    }
}
