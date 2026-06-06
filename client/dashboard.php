<?php

session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

echo "Bem-vindo, " . $_SESSION['user_name'];