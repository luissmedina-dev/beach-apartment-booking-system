<?php 

require_once("../config/connection.php");
require_once("../helpers/auth.php");

session_start();

verifyAdmin();

$id     = $_GET['id']     ?? null;
$action = $_GET['action'] ?? null;

// Mapeia cada ação permitida para o novo status correspondente.
// Centralizar aqui evita o bug anterior, onde ações sem mapeamento
// (approve_cancel / reject_cancel) chegavam até o UPDATE com $status indefinido.
$actionToStatus = [
    'confirm'        => 'confirmado',
    'cancel'         => 'cancelado',
    'approve_cancel' => 'cancelado',
    'reject_cancel'  => 'confirmado',
];

// Valida id e action antes de tocar no banco
if (empty($id) || !ctype_digit((string) $id) || !isset($actionToStatus[$action])) {
    $_SESSION['flash_error'] = 'Ação inválida ou reserva não encontrada.';
    header("Location: reservations.php");
    exit();
}

$status = $actionToStatus[$action];

$stmt = $conn->prepare("UPDATE reservations SET status = :status WHERE id = :id");
$stmt->bindParam(":status", $status);
$stmt->bindParam(":id", $id);
$stmt->execute();

$_SESSION['flash_success'] = 'Reserva atualizada com sucesso.';

// Mantém os filtros/paginação que o admin estava usando, se vieram via referer
header("Location: reservations.php");
exit();

?>
