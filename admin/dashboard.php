<?php

require_once("../helpers/auth.php");
require_once("../config/connection.php");

session_start();

verifyAdmin();


// Total de solicitacoes pendentes 
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'solicitado'");
$stmt->execute();

$pendingReservations = $stmt->fetchColumn();

// Lista de reservas
$status = $_GET['status'] ?? "";


$sql = "
        SELECT 
            reservations.id,
            users.name,
            reservations.checkin_date,
            reservations.checkout_date,
            reservations.total_price,
            reservations.status

        FROM reservations

        INNER JOIN users 
        ON reservations.user_id = users.id
        ";

if(!empty($status)){

    $sql .= " WHERE reservations.status = :status";

}

$stmt = $conn->prepare($sql);

if(!empty($status)){

    $stmt->bindParam(":status", $status);

}
$stmt->execute();

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="card">
    <h3>Reservas aguardando aprovação</h3>
    <p><?= $pendingReservations ?></p>
</div>
<form method="GET">

    <label>Status:</label>

    <select name="status">

        <option value="">Todos</option>
        <option value="solicitado">Solicitado</option>
        <option value="confirmado">Confirmado</option>
        <option value="cancelado">Cancelado</option>
        <option value="cancelamento solicitado">Cancelamento solicitado</option>

    </select>

    <button type="submit">
        Filtrar
    </button>

</form>
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Entrada</th>
            <th>Saída</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>

        <?php foreach($reservations as $reservation): ?>
        <tr>
            <td><?= htmlspecialchars($reservation['name']) ?></td>
            <td><?= date("d/m/Y", strtotime($reservation['checkin_date'])) ?></td>
            <td><?= date("d/m/Y", strtotime($reservation['checkout_date'])) ?></td>
            <td>R$<?= number_format($reservation['total_price'], 2, ',', '.') ?></td>
            <td><?= htmlspecialchars($reservation['status']) ?></td>
            <td>
                <?php if($reservation['status'] === "solicitado"): ?>
                    <a href="update_reservation.php?id=<?= $reservation['id'] ?>&action=confirm">Confirmar</a>
                    <a href="update_reservation.php?id=<?= $reservation['id'] ?>&action=cancel">Cancelar</a>
                <?php elseif($reservation['status'] === "cancelamento solicitado"): ?>
                    <a href="update_reservation.php?id=<?= $reservation['id'] ?>&action=approve_cancel">Aprovar cancelamento</a>
                    <a href="update_reservation.php?id=<?= $reservation['id'] ?>&action=reject_cancel">Recusar cancelamento</a>
                <?php elseif($reservation['status'] === "confirmado"): ?>
                    <span>Reserva confirmada</span>
                <?php elseif($reservation['status'] === "cancelado"): ?>
                    <span>Reserva cancelada</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>