<?php

session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once("../config/connection.php");

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Busca reservas do usuário (últimas 5 para o dashboard)
$stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id = :user_id ORDER BY checkin_date DESC LIMIT 5");
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contagens para os cards de resumo
$stmtTotal = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = :user_id");
$stmtTotal->bindParam(":user_id", $user_id);
$stmtTotal->execute();
$totalReservations = $stmtTotal->fetchColumn();

$stmtConfirmed = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = :user_id AND status = 'Confirmado'");
$stmtConfirmed->bindParam(":user_id", $user_id);
$stmtConfirmed->execute();
$confirmedReservations = $stmtConfirmed->fetchColumn();

$stmtValue = $conn->prepare("SELECT SUM(total_price) FROM reservations WHERE user_id = :user_id AND status != 'Cancelado'");
$stmtValue->bindParam(":user_id", $user_id);
$stmtValue->execute();
$totalValue = $stmtValue->fetchColumn() ?? 0;

// Iniciais do nome para o avatar
$nameParts = explode(" ", trim($user_name));
$initials  = strtoupper($nameParts[0][0] . (count($nameParts) > 1 ? end($nameParts)[0] : ""));

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Apartamento Bombinhas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard-page">

    <!-- Topbar -->
    <header class="dash-topbar">
        <a href="../public/index.php" class="dash-topbar-brand">Bombinhas</a>
        <div class="dash-topbar-user">
            <span class="dash-topbar-name">Olá, <?= htmlspecialchars(explode(" ", $user_name)[0]) ?></span>
            <div class="dash-topbar-avatar"><?= $initials ?></div>
            <a href="logout.php" class="dash-topbar-logout">Sair</a>
        </div>
    </header>

    <div class="dash-body">

        <!-- Sidebar -->
        <aside class="dash-sidebar">
            <p class="dash-sidebar-section">Menu</p>
            <a href="dashboard.php" class="active">
                <span class="icon">⊞</span> Dashboard
            </a>
            <a href="my_reservations.php">
                <span class="icon">📋</span> Minhas reservas
            </a>

            <p class="dash-sidebar-section">Site</p>
            <a href="../public/availability.php">
                <span class="icon">📅</span> Disponibilidade
            </a>
            <a href="../public/request_reservation.php">
                <span class="icon">＋</span> Solicitar reserva
            </a>
            <a href="../public/index.php">
                <span class="icon">🏠</span> Página inicial
            </a>
        </aside>

        <!-- Conteúdo principal -->
        <main class="dash-content">

            <h1 class="dash-page-title">Bem-vindo, <?= htmlspecialchars(explode(" ", $user_name)[0]) ?></h1>
            <p class="dash-page-subtitle">Confira suas reservas e informações da conta</p>

            <!-- Cards de resumo -->
            <div class="dash-summary-grid">
                <div class="dash-summary-card">
                    <p class="dash-summary-label">Total de reservas</p>
                    <p class="dash-summary-value"><?= $totalReservations ?></p>
                </div>
                <div class="dash-summary-card">
                    <p class="dash-summary-label">Confirmadas</p>
                    <p class="dash-summary-value"><?= $confirmedReservations ?></p>
                </div>
                <div class="dash-summary-card">
                    <p class="dash-summary-label">Total investido</p>
                    <p class="dash-summary-value gold">R$ <?= number_format($totalValue, 0, ',', '.') ?></p>
                </div>
            </div>

            <!-- Reservas recentes -->
            <div class="dash-section-header">
                <h2 class="dash-section-title">Reservas recentes</h2>
                <a href="my_reservations.php" class="dash-section-link">Ver todas →</a>
            </div>

            <?php if(empty($reservations)): ?>
                <div class="dash-empty">
                    <p>Você ainda não tem reservas.</p>
                    <a href="../public/request_reservation.php">Solicitar uma reserva</a>
                </div>
            <?php else: ?>
                <div class="dash-reservations-list">
                    <?php foreach($reservations as $reservation): ?>
                        <?php
                            $entrada = new DateTime($reservation['checkin_date']);
                            $saida   = new DateTime($reservation['checkout_date']);
                            $days    = $entrada->diff($saida)->days;

                            $statusClass = match(strtolower($reservation['status'])) {
                                'confirmado' => 'status-confirmado',
                                'cancelado'  => 'status-cancelado',
                                'concluído', 'concluido' => 'status-concluido',
                                default      => 'status-solicitado',
                            };
                        ?>
                        <div class="dash-reservation-card">
                            <span class="dash-res-id">#<?= $reservation['id'] ?></span>
                            <div class="dash-res-dates">
                                <strong>
                                    <?= $entrada->format('d/m/Y') ?> → <?= $saida->format('d/m/Y') ?>
                                </strong>
                                <span>Bombinhas, SC</span>
                            </div>
                            <span class="dash-res-nights"><?= $days ?> noite<?= $days != 1 ? 's' : '' ?></span>
                            <span class="dash-res-price">R$ <?= number_format($reservation['total_price'], 2, ',', '.') ?></span>
                            <span class="dash-res-status">
                                <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($reservation['status']) ?></span>
                                <?php if($reservation['status'] == 'Solicitado'): ?>
                                    <a href="cancel_request.php?id=<?= $reservation['id'] ?>" class="dash-res-cancel">Cancelar</a>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </main>

    </div>
</div>

</body>
</html>
