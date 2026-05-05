<?php
require_once 'inc/zkong_auth.php';

$login = zkong_login();
$token = $login['data']['token'];

echo "TOKEN: " . $token;
