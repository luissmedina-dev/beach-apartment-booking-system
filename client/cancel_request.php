<?php

require_once("../config/connection.php");
require_once("../dao/ReservationDAO.php");

session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Redireciona se não veio id ou se id não é inteiro válido
$reservation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(!$reservation_id) {
    header("Location: my_reservations.php");
    exit();
}

$user_id        = $_SESSION['user_id'];
$reservationDAO = new ReservationDAO($conn);

// Busca a reserva garantindo que pertence a este usuário
$stmt = $conn->prepare("SELECT status FROM reservations WHERE id = :id AND user_id = :user_id");
$stmt->bindParam(":id",      $reservation_id);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// Reserva não existe ou não pertence ao usuário logado
if(!$reservation) {
    header("Location: my_reservations.php");
    exit();
}

$currentStatus = $reservation['status'];

// Define o novo status de acordo com o fluxo correto:
// solicitado  → cancelado                (sem necessidade de aprovação)
// confirmado  → cancelamento solicitado  (admin precisa aprovar)
// Qualquer outro status não permite cancelamento pelo cliente
if($currentStatus === 'solicitado') {
    $newStatus = 'cancelado';
} elseif($currentStatus === 'confirmado') {
    $newStatus = 'cancelamento solicitado';
} else {
    // cancelado, cancelamento solicitado — não faz nada
    header("Location: my_reservations.php");
    exit();
}

$reservationDAO->updateStatus($reservation_id, $newStatus);

header("Location: my_reservations.php");
exit();
?>
