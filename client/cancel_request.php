<?php

require_once("../config/connection.php");

session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])){

    $reservation_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE reservations 
                            SET status = 'cancelado'
                            WHERE id = :id
                            AND user_id = :user_id"
                            );
    
    $stmt->bindParam(":id", $reservation_id);
    $stmt->bindParam(":user_id", $user_id);

    $stmt->execute();

    header("Location: my_reservations.php");
    exit();

}

?>