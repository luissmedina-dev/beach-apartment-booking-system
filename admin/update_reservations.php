<?php 

require_once("../config/connection.php");
require_once("../helpers/auth.php");
require_once("../helpers/flash.php");
require_once("../dao/ReservationDAO.php");

session_start();

verifyAdmin();

if(!isset($_GET['id'], $_GET['action'])){
    header("Location: reservations.php");
    exit();
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$action = trim($_GET['action']);

$reservationDAO = new ReservationDAO($conn);


// Mapeia cada ação permitida para o novo status correspondente.
$actionToStatus = [
    'confirm'        => 'confirmado',
    'cancel'         => 'cancelado',
    'approve_cancel' => 'cancelado',
    'reject_cancel'  => 'confirmado',
];

if(!array_key_exists($action, $actionToStatus)){
    header("Location: reservations.php");
    exit();
}

// Valida id e action antes de tocar no banco
if (empty($id) || !isset($actionToStatus[$action])) {
    setFlash("error","Ação inválida ou reserva não encontrada.");
    header("Location: reservations.php");
    exit();
}

$status = $actionToStatus[$action];

$reservation = $reservationDAO->findByID($id);

$currentStatus = $reservation['status'];

$allowedActions = [
    "solicitado" => [
        "confirm",
        "cancel",
    ],

    "confirmado" => [

    ],

    "cancelamento solicitado" => [
        "approve_cancel",
        "reject_cancel"
    ],

    "cancelado" => [

    ]
];


if(!isset($allowedActions[$currentStatus]) || !in_array($action, $allowedActions[$currentStatus])){
    setFlash("error","Essa ação não é permitida.");
    header("Location: reservations.php");
    exit();
}

if(!$reservation){
    header("Location: reservation.php");
    exit();
}

if($reservation['status'] === "cancelado" && $action === "confirm"){
    setFlash("error", "Reserva cancelada não pode ser confirmada.");
    header("Location: reservations.php");
    exit();
}

$reservationDAO->updateStatus($id, $status);

// Mensagem de sucesso
setFlash("success","Reserva atualizada com sucesso.");

// Mantém os filtros/paginação que o admin estava usando, se vieram via referer
header("Location: reservations.php");
exit();

?>
