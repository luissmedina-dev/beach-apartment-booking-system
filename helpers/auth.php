<?php 

function verifyAdmin() {

    if(!isset($_SESSION['user_id'])){

        header("Location: ../client/login.php");
        exit();

    }

    if($_SESSION['user_role'] !== "admin"){

        header("Location: ../client/login.php");
        exit();

    }
}