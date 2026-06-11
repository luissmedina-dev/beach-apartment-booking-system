<?php 

require_once("../config/connection.php");

session_start();

$errors = [];

if(!isset($_SESSION['user_id'])) {
    header("Location: ../client/login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $checkin = filter_input(INPUT_POST, "checkin");
    $checkout = filter_input(INPUT_POST, "checkout");

    if($checkin >= $checkout) {
        $errors[] = "A data de saída deve ser maior que a data de entrada.";
    }

    if(empty($errors)){

        $user_id =  $_SESSION['user_id'];
        $status = 'Solicitado';

        $daily_price = 300;

        $entrada = new DateTime($checkin);
        $saida = new DateTime($checkout);

        $difference = $entrada->diff($saida);

        $days = $difference->days;

        $total_price = $days * $daily_price;


        $stmt = $conn->prepare("INSERT INTO reservations
                                (user_id, checkin_date, checkout_date, total_price, status)
                                VALUES 
                                (:user_id, :checkin, :checkout, :total_price, :status)");

        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":checkin", $checkin);
        $stmt->bindParam(":checkout", $checkout);
        $stmt->bindParam(":total_price", $total_price);
        $stmt->bindParam(":status", $status);

        $stmt->execute();

        echo "Solicitação enviada com sucesso!";
    }

}

?>

<?php 

    require_once("../templates/header.php");
    require_once("../templates/navbar.php");

?>

<?php if(!empty($errors)): ?>
    <div class="error-message">
        <?php foreach($errors as $error): ?>
            <p><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<section class="reservartion">
    <h1>Solicitar Reserva</h1>
    <form action="" method="post">
        <div>
            <label for="checkin">Data de entrada:</label>
            <input type="date" name="checkin" id="checkin" required>
        </div>
        <div>
            <label for="checkout">Data de saída:</label>
            <input type="date" name="checkout" id="checkout" required>
        </div>
        <button type="submit">Solicitar Reserva</button>
    </form>
</section>

<?php 
    
    require_once("../templates/footer.php");

?>