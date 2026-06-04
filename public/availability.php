<?php 
    require_once("../templates/header.php");
    require_once("../templates/navbar.php");

    $dias = [];

    for ($dia = 1; $dia <= 28; $dia++) {
            array_push($dias , $dia);
    }

?>

<section class="availability">
    <h1>Agende sua estadia</h1>
    <p>Consulte abaixo os períodos disponíveis para hospedagem.</p>
    <div class="calendar">
        <?php foreach($dias as $dia): ?>
            <div class="calendar-day availible"><?= $dia ?></div>
        <?php endforeach ?>
    </div>
    <div class="caption">
        <h2>Legenda</h2>
        <p>🟢 Disponível</p>
        <p>🔴 Reservado</p>
    </div>
</section>

<?php
    require_once("../templates/footer.php");
?>