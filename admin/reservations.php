<?php

require_once("../helpers/auth.php");
require_once("../config/connection.php");
require_once("../helpers/flash.php");
require_once("../dao/ReservationDAO.php");

session_start();

verifyAdmin();

$reservationDAO = new ReservationDAO($conn);

// ── Filtros vindos da URL ──────────────────────────────────────────────
$status    = $_GET['status']     ?? "";
$search    = trim($_GET['search'] ?? "");
$dateFrom  = $_GET['date_from']  ?? "";
$dateTo    = $_GET['date_to']    ?? "";

$reservations = $reservationDAO->getReservationsFiltered($status, $search, $dateFrom, $dateTo);

// ── Contagem por status (para os contadores no topo dos filtros) ───────
$countSolicitado = $reservationDAO->countByStatus("solicitado");

$countConfirmado = $reservationDAO->countByStatus("confirmado");

$countCancelado = $reservationDAO->countByStatus("cancelado");

$countCancelamentoSolicitado = $reservationDAO->countByStatus("cancelamento solicitado");

// ── Mensagens flash (definidas em update_reservations.php) ─────────────
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

require_once("../templates/admin_header.php");
?>

<script>document.getElementById('adminTopbarTitle').textContent = 'Reservas';</script>

<div class="admin-page-intro">
    <h1>Gerenciamento de Reservas</h1>
    <p>Acompanhe, confirme e gerencie todas as solicitações de reserva do apartamento.</p>
</div>

<?php if ($flashSuccess): ?>
    <div class="admin-flash admin-flash--success"><?= htmlspecialchars($flashSuccess) ?></div>
<?php endif; ?>
<?php if ($flashError): ?>
    <div class="admin-flash admin-flash--error"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>

<!-- ── Resumo rápido por status ── -->
<div class="res-status-summary">
    <a href="?status=solicitado" class="res-status-chip res-status-chip--solicitado <?= $status === 'solicitado' ? 'is-active' : '' ?>">
        <span class="res-status-chip-count"><?= $countSolicitado ?></span> Solicitadas
    </a>
    <a href="?status=confirmado" class="res-status-chip res-status-chip--confirmado <?= $status === 'confirmado' ? 'is-active' : '' ?>">
        <span class="res-status-chip-count"><?= $countConfirmado ?></span> Confirmadas
    </a>
    <a href="?status=cancelamento+solicitado" class="res-status-chip res-status-chip--cancelamento <?= $status === 'cancelamento solicitado' ? 'is-active' : '' ?>">
        <span class="res-status-chip-count"><?= $countCancelamentoSolicitado ?></span> Cancel. solicitado
    </a>
    <a href="?status=cancelado" class="res-status-chip res-status-chip--cancelado <?= $status === 'cancelado' ? 'is-active' : '' ?>">
        <span class="res-status-chip-count"><?= $countCancelado ?></span> Canceladas
    </a>
    <?php if (!empty($status) || !empty($search) || !empty($dateFrom) || !empty($dateTo)): ?>
        <a href="reservations.php" class="res-status-chip res-status-chip--clear">Limpar filtros ✕</a>
    <?php endif; ?>
</div>

<!-- ── Painel de filtros ── -->
<div class="admin-panel res-filters-panel">
    <form method="GET" class="res-filters-form">

        <div class="res-filter-field res-filter-field--search">
            <label for="search">Buscar cliente</label>
            <div class="res-search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="res-search-icon"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <input type="text" name="search" id="search" placeholder="Nome do cliente..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>

        <div class="res-filter-field">
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="" <?= $status === '' ? 'selected' : '' ?>>Todos</option>
                <option value="solicitado" <?= $status === 'solicitado' ? 'selected' : '' ?>>Solicitado</option>
                <option value="confirmado" <?= $status === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                <option value="cancelado" <?= $status === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                <option value="cancelamento solicitado" <?= $status === 'cancelamento solicitado' ? 'selected' : '' ?>>Cancelamento solicitado</option>
            </select>
        </div>

        <div class="res-filter-field">
            <label for="date_from">De</label>
            <input type="date" name="date_from" id="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
        </div>

        <div class="res-filter-field">
            <label for="date_to">Até</label>
            <input type="date" name="date_to" id="date_to" value="<?= htmlspecialchars($dateTo) ?>">
        </div>

        <button type="submit" class="res-filter-submit">Filtrar</button>
    </form>
</div>

<!-- ── Tabela / cards de reservas ── -->
<div class="admin-panel res-table-panel">

    <div class="admin-panel-header">
        <div>
            <h2>Todas as reservas</h2>
            <p><?= count($reservations) ?> resultado<?= count($reservations) != 1 ? 's' : '' ?> encontrado<?= count($reservations) != 1 ? 's' : '' ?></p>
        </div>
    </div>

    <?php if (empty($reservations)): ?>
        <div class="admin-empty">
            <p>Nenhuma reserva encontrada com os filtros selecionados.</p>
        </div>
    <?php else: ?>

        <!-- Tabela — visível em telas maiores -->
        <?php showFlash(); ?>
        <div class="admin-table-wrap res-table-desktop">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
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
                            <td>R$ <?= number_format($reservation['total_price'], 2, ',', '.') ?></td>
                            <td><span class="admin-badge <?= $statusClass ?>"><?= htmlspecialchars($reservation['status']) ?></span></td>
                            <td>
                                <?php if ($statusKey === "solicitado"): ?>
                                    <div class="res-actions">
                                        <a href="update_reservations.php?id=<?= $reservation['id'] ?>&action=confirm" class="res-btn res-btn--confirm">Confirmar</a>
                                        <a href="update_reservations.php?id=<?= $reservation['id'] ?>&action=cancel" class="res-btn res-btn--cancel"
                                           onclick="return confirm('Cancelar esta reserva?')">Cancelar</a>
                                    </div>
                                <?php elseif ($statusKey === "cancelamento solicitado"): ?>
                                    <div class="res-actions">
                                        <a href="update_reservations.php?id=<?= $reservation['id'] ?>&action=approve_cancel" class="res-btn res-btn--cancel">Aprovar cancelamento</a>
                                        <a href="update_reservations.php?id=<?= $reservation['id'] ?>&action=reject_cancel" class="res-btn res-btn--confirm">Recusar cancelamento</a>
                                    </div>
                                <?php elseif ($statusKey === "confirmado"): ?>
                                    <span class="res-static-label res-static-label--confirmado">Reserva confirmada</span>
                                <?php elseif ($statusKey === "cancelado"): ?>
                                    <span class="res-static-label res-static-label--cancelado">Reserva cancelada</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Cards — visíveis no mobile -->
        <div class="res-cards-mobile">
            <?php foreach ($reservations as $reservation): ?>
                <?php
                    $statusKey = strtolower($reservation['status']);
                    $statusClass = match($statusKey) {
                        'confirmado' => 'admin-badge--confirmado',
                        'cancelado'  => 'admin-badge--cancelado',
                        'cancelamento solicitado' => 'admin-badge--cancelamento',
                        default      => 'admin-badge--solicitado',
                    };
                ?>
                <div class="res-card">
                    <div class="res-card-top">
                        <div class="admin-table-client">
                            <span class="admin-table-avatar"><?= strtoupper(substr($reservation['name'], 0, 1)) ?></span>
                            <?= htmlspecialchars($reservation['name']) ?>
                        </div>
                        <span class="admin-badge <?= $statusClass ?>"><?= htmlspecialchars($reservation['status']) ?></span>
                    </div>

                    <div class="res-card-dates">
                        <div>
                            <span class="res-card-label">Check-in</span>
                            <strong><?= date("d/m/Y", strtotime($reservation['checkin_date'])) ?></strong>
                        </div>
                        <div>
                            <span class="res-card-label">Check-out</span>
                            <strong><?= date("d/m/Y", strtotime($reservation['checkout_date'])) ?></strong>
                        </div>
                        <div>
                            <span class="res-card-label">Valor</span>
                            <strong>R$ <?= number_format($reservation['total_price'], 2, ',', '.') ?></strong>
                        </div>
                    </div>

                    <div class="res-card-actions">
                        <?php if ($statusKey === "solicitado"): ?>
                            <a href="update_reservations.php?id=<?= $reservation['id'] ?>&action=confirm" class="res-btn res-btn--confirm res-btn--block">Confirmar</a>
                            <a href="update_reservations.php?id=<?= $reservation['id'] ?>&action=cancel" class="res-btn res-btn--cancel res-btn--block"
                               onclick="return confirm('Cancelar esta reserva?')">Cancelar</a>
                        <?php elseif ($statusKey === "cancelamento solicitado"): ?>
                            <a href="update_reservations.php?id=<?= $reservation['id'] ?>&action=approve_cancel" class="res-btn res-btn--cancel res-btn--block">Aprovar cancelamento</a>
                            <a href="update_reservations.php?id=<?= $reservation['id'] ?>&action=reject_cancel" class="res-btn res-btn--confirm res-btn--block">Recusar cancelamento</a>
                        <?php elseif ($statusKey === "confirmado"): ?>
                            <span class="res-static-label res-static-label--confirmado">Reserva confirmada</span>
                        <?php elseif ($statusKey === "cancelado"): ?>
                            <span class="res-static-label res-static-label--cancelado">Reserva cancelada</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<?php require_once("../templates/admin_footer.php"); ?>
