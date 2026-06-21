<?php

require_once("../helpers/auth.php");
require_once("../config/connection.php");

session_start();

verifyAdmin();


// Total de solicitacoes pendentes 
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'solicitado'");
$stmt->execute();

$pendingReservations = $stmt->fetchColumn();

// Reservas confirmadas
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'confirmado'");
$stmt->execute();

$confirmReservations = $stmt->fetchColumn();

// Cancelamentos pendentes 
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'cancelamento solicitado'");
$stmt->execute();

$pendingCancellations = $stmt->fetchColumn();

// Reservas canceladas
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'cancelado'");
$stmt->execute();

$cancelReservations = $stmt->fetchColumn();

// Receita estimada
$stmt = $conn->prepare("SELECT SUM(total_price) FROM reservations WHERE status = 'confirmado'");
$stmt->execute();

$estimatedRevenue = $stmt->fetchColumn();

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

$sql .= " ORDER BY reservations.created_at DESC LIMIT 5";

$stmt = $conn->prepare($sql);

if(!empty($status)){

    $stmt->bindParam(":status", $status);

}
$stmt->execute();

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once("../templates/admin_header.php");
?>

<script>document.getElementById('adminTopbarTitle').textContent = 'Dashboard';</script>

<div class="admin-page-intro">
    <h1>Visão geral</h1>
    <p>Acompanhe reservas, cancelamentos e receita do apartamento em tempo real.</p>
</div>

<!-- ── Cards de métricas ── -->
<div class="dashboard-cards">

    <div class="dashboard-card dashboard-card--amber">
        <div class="dashboard-card-top">
            <span class="dashboard-card-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7V12L15 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="dashboard-card-trend">Aguardando</span>
        </div>
        <p class="dashboard-card-value"><?= $pendingReservations ?></p>
        <h3>Reservas solicitadas</h3>
        <div class="dashboard-card-wave"></div>
    </div>

    <div class="dashboard-card dashboard-card--teal">
        <div class="dashboard-card-top">
            <span class="dashboard-card-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M5 12.5L9.5 17L19 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="dashboard-card-trend">Ativas</span>
        </div>
        <p class="dashboard-card-value"><?= $confirmReservations ?></p>
        <h3>Reservas confirmadas</h3>
        <div class="dashboard-card-wave"></div>
    </div>

    <div class="dashboard-card dashboard-card--coral">
        <div class="dashboard-card-top">
            <span class="dashboard-card-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 8V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="16.2" r="1" fill="currentColor"/><path d="M10.3 3.9L2.7 17.5C2.1 18.5 2.8 19.8 4 19.8H20C21.2 19.8 21.9 18.5 21.3 17.5L13.7 3.9C13.1 2.9 11.9 2.9 10.3 3.9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
            </span>
            <span class="dashboard-card-trend">Requer ação</span>
        </div>
        <p class="dashboard-card-value"><?= $pendingCancellations ?></p>
        <h3>Cancelamentos pendentes</h3>
        <div class="dashboard-card-wave"></div>
    </div>

    <div class="dashboard-card dashboard-card--navy">
        <div class="dashboard-card-top">
            <span class="dashboard-card-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 3V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M16.5 6.5C16.5 5.1 14.5 4 12 4C9.5 4 7.5 5.3 7.5 7C7.5 8.7 9 9.4 12 10C15 10.6 16.5 11.5 16.5 13.2C16.5 14.9 14.5 16.2 12 16.2C9.5 16.2 7.5 15.1 7.5 13.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </span>
            <span class="dashboard-card-trend">Confirmadas</span>
        </div>
        <p class="dashboard-card-value">R$ <?= number_format($estimatedRevenue ?? 0, 2, ',', '.') ?></p>
        <h3>Receita estimada</h3>
        <div class="dashboard-card-wave"></div>
    </div>

</div>

<!-- ── Lista de reservas recentes ── -->
<div class="admin-panel">

    <div class="admin-panel-header">
        <div>
            <h2>Reservas recentes</h2>
            <p>Últimas 5 solicitações registradas no sistema</p>
        </div>

        <form method="GET" class="admin-filter-form">
            <label for="status">Status</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="" <?= $status === '' ? 'selected' : '' ?>>Todos</option>
                <option value="solicitado" <?= $status === 'solicitado' ? 'selected' : '' ?>>Solicitado</option>
                <option value="confirmado" <?= $status === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                <option value="cancelado" <?= $status === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                <option value="cancelamento solicitado" <?= $status === 'cancelamento solicitado' ? 'selected' : '' ?>>Cancelamento solicitado</option>
            </select>
            <noscript><button type="submit" class="admin-filter-btn">Filtrar</button></noscript>
        </form>
    </div>

    <?php if(empty($reservations)): ?>
        <div class="admin-empty">
            <p>Nenhuma reserva encontrada para este filtro.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Entrada</th>
                        <th>Saída</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reservations as $reservation): ?>
                        <?php
                            $statusKey = strtolower($reservation['status']);
                            $statusClass = match($statusKey) {
                                'confirmado' => 'admin-badge--confirmado',
                                'cancelado'  => 'admin-badge--cancelado',
                                'cancelamento solicitado' => 'admin-badge--cancelamento',
                                default      => 'admin-badge--solicitado',
                            };
                        ?>
                        <tr>
                            <td>
                                <div class="admin-table-client">
                                    <span class="admin-table-avatar"><?= strtoupper(substr($reservation['name'], 0, 1)) ?></span>
                                    <?= htmlspecialchars($reservation['name']) ?>
                                </div>
                            </td>
                            <td><?= date("d/m/Y", strtotime($reservation['checkin_date'])) ?></td>
                            <td><?= date("d/m/Y", strtotime($reservation['checkout_date'])) ?></td>
                            <td><span class="admin-badge <?= $statusClass ?>"><?= htmlspecialchars($reservation['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <a href="reservations.php" class="admin-panel-link">Ver todas as reservas →</a>
</div>

<?php require_once("../templates/admin_footer.php"); ?>
