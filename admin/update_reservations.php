<?php 

require_once("../config/connection.php");
require_once("../helpers/auth.php");
require_once("../helpers/flash.php");

session_start();

verifyAdmin();

if(!isset($_GET['id'], $_GET['action'])){
    header("Location: reservations.php");
    exit();
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$action = trim($_GET['action']);


// Mapeia cada ação permitida para o novo status correspondente.
// Centralizar aqui evita o bug anterior, onde ações sem mapeamento
// (approve_cancel / reject_cancel) chegavam até o UPDATE com $status indefinido.
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

$stmt = $conn->prepare("SELECT status FROM reservations WHERE id = :id");
$stmt->bindParam(":id", $id);
$stmt->execute();

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

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

$stmt = $conn->prepare("UPDATE reservations SET status = :status WHERE id = :id");
$stmt->bindParam(":status", $status);
$stmt->bindParam(":id", $id);
$stmt->execute();

// Mensagem de sucesso
setFlash("success","Reserva atualizada com sucesso.");

// Mantém os filtros/paginação que o admin estava usando, se vieram via referer
header("Location: reservations.php");
exit();

?>
