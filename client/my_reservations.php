<?php

require_once("../config/connection.php");
require_once("../dao/ReservationDAO.php");

session_start();
$resevationDAO = new ReservationDAO($conn);

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$reservations = $resevationDAO->getReservationsByUser($user_id);

require_once("../templates/header.php");
require_once("../templates/navbar.php");
?>

<section class="myres-page">

    <div class="myres-header">
        <div>
            <h1>Minhas reservas</h1>
            <p>Acompanhe o status de todas as suas estadias em Bombinhas.</p>
        </div>
        <a href="../public/request_reservation.php" class="myres-new-btn">+ Nova reserva</a>
    </div>

    <?php if(empty($reservations)): ?>
        <div class="myres-empty">
            <div class="myres-empty-icon">📋</div>
            <h2>Nenhuma reserva ainda</h2>
            <p>Você ainda não fez nenhuma solicitação de reserva. Verifique a disponibilidade e garanta sua estadia!</p>
            <a href="../public/availability.php" class="myres-empty-btn">Ver disponibilidade</a>
        </div>
    <?php else: ?>
        <div class="myres-list">
            <?php foreach($reservations as $r):
                $entrada = new DateTime($r['checkin_date']);
                $saida   = new DateTime($r['checkout_date']);
                $days    = $entrada->diff($saida)->days;

                $statusClass = match(strtolower($r['status'])) {
                    'confirmado'           => 'badge-confirmado',
                    'cancelado'            => 'badge-cancelado',
                    'concluído','concluido'=> 'badge-concluido',
                    default                => 'badge-solicitado',
                };
            ?>
            <div class="myres-card">
                <div class="myres-card-id">#<?= $r['id'] ?></div>

                <div class="myres-card-dates">
                    <strong><?= $entrada->format('d/m/Y') ?> → <?= $saida->format('d/m/Y') ?></strong>
                    <span><?= $days ?> noite<?= $days != 1 ? 's' : '' ?> · Bombinhas, SC</span>
                </div>

                <div class="myres-card-price">
                    R$ <?= number_format($r['total_price'], 2, ',', '.') ?>
                </div>

                <div class="myres-card-status">
                    <span class="myres-badge <?= $statusClass ?>"><?= htmlspecialchars($r['status']) ?></span>
                </div>

                <?php if($r['status'] == 'solicitado'): ?>
                    <a href="cancel_request.php?id=<?= $r['id'] ?>" class="myres-cancel-btn"
                       onclick="return confirm('Tem certeza que deseja cancelar esta reserva?')">
                        Cancelar
                    </a>
                <?php else: ?>
                    <div class="myres-cancel-placeholder"></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>

<?php require_once("../templates/footer.php"); ?>
