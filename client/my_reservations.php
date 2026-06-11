<?php 

require_once("../config/connection.php");

session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM reservations
                        WHERE user_id = :user_id");

$stmt->bindParam(":user_id", $user_id);

$stmt->execute();

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php 

require_once("../templates/header.php");
require_once("../templates/navbar.php");

?>

<section class="my-reservations">
    <h1>Minhas Reservas</h1>

    <?php if(empty($reservations)): ?>
        <p>Você ainda não possui reservas.</p>
    <?php else: ?>
        <?php foreach($reservations as $reservation): ?>
            <div class="reservation-card">

                <h3>Reserva #<?= $reservation['id'] ?></h3>

                <p>
                    Entrada:
                    <?= $reservation['checkin_date']; ?>
                </p>

                <p>
                    Saída:
                    <?= $reservation['checkout_date']; ?>
                </p>

                <p>
                    Status:
                    <?= $reservation['status']; ?>
                </p>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</section>

<?php 

require_once("../templates/footer.php");

?>