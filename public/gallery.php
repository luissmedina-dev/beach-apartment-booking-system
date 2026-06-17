<?php
    require_once("../templates/header.php");
    require_once("../templates/navbar.php");
?>

<section class="gallery-page">
    <div class="gallery-page-header">
        <span class="section-tag">Fotos</span>
        <h1>Cada detalhe foi pensado<br>para o seu conforto</h1>
        <p>Explore os ambientes do apartamento e imagine suas próximas férias em Bombinhas.</p>
    </div>

    <div class="gallery-grid">
        <div class="gallery-item gallery-item--wide">
            <img src="../assets/images/bombinhas.jpeg" alt="Sala de estar">
            <span class="gallery-label">Sala de estar</span>
        </div>
        <div class="gallery-item">
            <img src="../assets/images/bombinhas.jpeg" alt="Quarto principal">
            <span class="gallery-label">Quarto principal</span>
        </div>
        <div class="gallery-item">
            <img src="../assets/images/bombinhas.jpeg" alt="Segundo quarto">
            <span class="gallery-label">Segundo quarto</span>
        </div>
        <div class="gallery-item">
            <img src="../assets/images/bombinhas.jpeg" alt="Cozinha">
            <span class="gallery-label">Cozinha equipada</span>
        </div>
        <div class="gallery-item gallery-item--wide">
            <img src="../assets/images/bombinhas.jpeg" alt="Sacada com vista">
            <span class="gallery-label">Sacada com vista</span>
        </div>
        <div class="gallery-item">
            <img src="../assets/images/bombinhas.jpeg" alt="Banheiro">
            <span class="gallery-label">Banheiro</span>
        </div>
        <div class="gallery-item">
            <img src="../assets/images/bombinhas.jpeg" alt="Área de serviço">
            <span class="gallery-label">Área de serviço</span>
        </div>
        <div class="gallery-item">
            <img src="../assets/images/bombinhas.jpeg" alt="Garagem">
            <span class="gallery-label">Garagem privativa</span>
        </div>
    </div>

    <div class="gallery-cta-bar">
        <p>Gostou? Verifique a disponibilidade e garanta a sua reserva.</p>
        <a href="../public/availability.php" class="gallery-cta-btn">Ver disponibilidade</a>
    </div>
</section>

<?php require_once("../templates/footer.php"); ?>
