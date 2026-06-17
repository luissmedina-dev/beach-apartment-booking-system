<?php
    require_once("../templates/header.php");
    require_once("../templates/navbar.php");
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <span class="hero-tag">✦ Bombinhas, Santa Catarina</span>
        <h1>Acorde com o mar<br>pela janela</h1>
        <p>Apartamento completo a 250m da praia, para até 6 pessoas. Conforto, privacidade e Bombinhas ao seu redor.</p>
        <div class="hero-actions">
            <a href="../public/availability.php" class="hero-btn-primary">Ver disponibilidade</a>
            <a href="../public/gallery.php" class="hero-btn-ghost">Conhecer o apartamento</a>
        </div>
    </div>
</section>

<!-- SOBRE -->
<section class="about">
    <div class="image-apartment">
        <img src="../assets/images/bombinhas.jpeg" alt="Vista de Bombinhas">
    </div>
    <div class="about-content">
        <span class="section-tag">O apartamento</span>
        <h2>Tudo que você precisa,<br>pertinho da praia</h2>
        <p>Dois quartos aconchegantes, cozinha totalmente equipada, garagem privativa e Wi-Fi de alta velocidade. Pensado para quem quer descansar de verdade, sem abrir mão do conforto.</p>
        <p>Localizado a apenas 250 metros da praia e perto de restaurantes, mercados e pontos turísticos, é o ponto de partida perfeito para descobrir o melhor de Bombinhas.</p>
        <a href="../public/availability.php" class="about-cta">Verificar disponibilidade →</a>
    </div>
</section>

<!-- DESTAQUES -->
<section class="features">
    <span class="section-tag" style="color: var(--primary-color);">O que está incluído</span>
    <h2>Estrutura completa para sua estadia</h2>
    <div class="features-container">
        <div class="feature-card">
            <div class="feature-icon">🛏</div>
            <h3>2 Quartos</h3>
            <p>Comporta até 6 hóspedes com conforto</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🚗</div>
            <h3>Garagem</h3>
            <p>Vaga privativa coberta incluída</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📶</div>
            <h3>Wi-Fi</h3>
            <p>Internet rápida em todos os ambientes</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🏖</div>
            <h3>250m da praia</h3>
            <p>A pé até as melhores praias da cidade</p>
        </div>
    </div>
</section>

<!-- PRÉVIA DA GALERIA -->
<section class="gallery-preview">
    <span class="section-tag">Fotos</span>
    <h2>Conheça cada detalhe</h2>
    <p class="gallery-preview-sub">Ambientes pensados para o seu conforto e o da sua família</p>
    <div class="gallery-container">
        <img src="../assets/images/bombinhas.jpeg" alt="Sala de estar">
        <img src="../assets/images/bombinhas.jpeg" alt="Quarto">
        <img src="../assets/images/bombinhas.jpeg" alt="Vista da praia">
    </div>
    <div class="gallery-button">
        <a href="../public/gallery.php" class="gallery-cta">Ver todas as fotos</a>
    </div>
</section>

<!-- LOCALIZAÇÃO -->
<section class="location">
    <div class="location-content">
        <span class="section-tag">Localização</span>
        <h2>No coração de Bombinhas</h2>
        <p>A 250m da praia, perto de mercados, restaurantes e fácil acesso às praias mais bonitas de Santa Catarina. Você chega e já está no ritmo das férias.</p>
        <p>📍 Bombinhas — Santa Catarina</p>
    </div>
    <div class="location-map">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2510.404401393626!2d-48.4893695549213!3d-27.148487790962626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94d8a41219e90a85%3A0x1a6f69cedb41792c!2sPraia%20de%20Bombinhas!5e0!3m2!1spt-BR!2sbr!4v1780538079361!5m2!1spt-BR!2sbr"
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>

<?php require_once("../templates/footer.php"); ?>
