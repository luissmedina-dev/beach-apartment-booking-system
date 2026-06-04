<?php

    require_once("../templates/header.php");
    require_once("../templates/navbar.php");

?>

<section class="hero">
    <div class="hero-content">
        <h1>Apartamento em Bombinhas-SC</h1>
        <p>Conforto e tranquilidade para suas férias na praia</p>
        <button>Ver Disponibilidade</button>
    </div>
</section>

<section class="about">
    <div class="image-apartment">
        <img src="../assets/images/bombinhas.jpeg" alt="">
    </div>
    <div class="about-content">
        <h2>Sobre o apartamento</h2>
        <p>Apartamento localizado em Bombinhas-SC, ideal para famílias que buscam conforto, tranquilidade e proximidade com a praia. Conta com quartos mobiliados, cozinha equipada, garagem, Wi-Fi e fácil acesso aos principais pontos turísticos da região.</p>
        <p>Nosso apartamento foi pensado para oferecer conforto, privacidade e momentos inesquecíveis para você e sua familia. Ambientes aconchegantes, bem equipados e em uma localização privilegiada, pertinho da praia e do centro de Bombinhas. </p>
        <p>Aqui você encontra o equilíbrio perfeito entre descanso, comodidade e a beleza natural de um dos destinos mais bonitos de Santa Catarina.</p>
    </div>
</section>

<section class="features">
        <h2>Informacoes Principais</h2>
    <div class="features-container">

        <div class="feature-card">
            <h3>2 Quartos</h3>
            <p>Acomoda ate 6 pessoas</p>
        </div>

        <div class="feature-card">
            <h3>Garagem</h3>
            <p>vaga privativa para seu veiculo</p>
        </div>

        <div class="feature-card">
            <h3>Wi-Fi</h3>
            <p>Internet rápida disponível</p>
        </div>

        <div class="feature-card">
            <h3>Próximo da praia</h3>
            <p>Apenas 250 metros da praia</p>
        </div>
    </div>
</section>

<section class="gallery-preview">
    <h2>Gallery</h2>
    <div class="gallery-container">
        <img src="../assets/images/bombinhas.jpeg" alt="Imagem 1">
        <img src="../assets/images/bombinhas.jpeg" alt="Imagem 2">
        <img src="../assets/images/bombinhas.jpeg" alt="Imagem 3">
    </div>
    <div class="gallery-button">
        <button>Ver galeria completa</button>
    </div>
</section>

<section class="location">
    <div class="location-content">
        <h2>Localização</h2>
        <p>Localizado em uma região tranquila e segura de Bombinhas,
            perto de mercados, restaurantes e com fácil acesso às
            principais praias da cidade. </p>
        <p>📍 Bombinhas - Santa Catarina</p>
    </div>
    <div class="location-map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2510.404401393626!2d-48.4893695549213!3d-27.148487790962626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94d8a41219e90a85%3A0x1a6f69cedb41792c!2sPraia%20de%20Bombinhas!5e0!3m2!1spt-BR!2sbr!4v1780538079361!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

<?php
    require_once("../templates/footer.php");