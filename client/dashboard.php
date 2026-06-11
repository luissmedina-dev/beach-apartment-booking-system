<?php

session_start();

if(!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

require_once("../templates/header.php");
require_once("../templates/navbar.php");

?>


<section class="dashboard">

    <h1>Bem-vindo, <?= $_SESSION['user_name']; ?></h1>

    <div class="dashboard-card">
        <h2>Minhas reservas</h2>
        <p>Consulte suas reservas realizadas.</p>
        <a href="my_reservations.php">Ver reservas</a>
        <a href="logout.php">Sair</a>
    </div>


</section>


<?php

require_once("../templates/footer.php");

?>