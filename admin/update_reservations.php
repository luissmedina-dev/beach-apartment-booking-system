<?php 

require_once("../config/connection.php");
require_once("../helpers/auth.php");

session_start();

verifyAdmin();

$id = $_GET['id'];
$action = $_GET['action'];

if($action === "confirm"){

    $status = "confirmado";

}

if($action === "cancel"){

    $status = "cancelado";

}

$stmt = $conn->prepare("UPDATE reservations SET status = :status WHERE id = :id");
$stmt->bindParam(":status", $status);
$stmt->bindParam(":id", $id);
$stmt->execute();

header("Location: dashboard.php");
exit();

?>