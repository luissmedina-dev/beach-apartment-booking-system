<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$prefix = '../'; 

function navActive($page, $current) {
    return $page === $current ? 'nav-active' : '';
}
?>
<nav class="navbar">
    <div class="nav-brand">
        <a href="<?= $prefix ?>public/index.php" class="nav-logo">
            <div class="nav-logo-icon">B</div>
            <span class="nav-logo-text">Bombinhas</span>
        </a>
    </div>

    <ul class="nav-links">
        <li><a href="<?= $prefix ?>public/index.php" class="<?= navActive('index.php', $currentPage) ?>">Home</a></li>
        <li><a href="<?= $prefix ?>public/gallery.php" class="<?= navActive('gallery.php', $currentPage) ?>">Fotos</a></li>
        <li><a href="<?= $prefix ?>public/availability.php" class="<?= navActive('availability.php', $currentPage) ?>">Disponibilidade</a></li>
        <li><a href="<?= $prefix ?>public/rules.php" class="<?= navActive('rules.php', $currentPage) ?>">Regras</a></li>
        <li><a href="<?= $prefix ?>public/index.php#contact">Contato</a></li>
    </ul>

    <div class="nav-auth">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="<?= $prefix ?>client/dashboard.php" class="nav-btn nav-btn-outline">Minha conta</a>
            <a href="<?= $prefix ?>client/logout.php" class="nav-btn nav-btn-ghost">Sair</a>
        <?php else: ?>
            <a href="<?= $prefix ?>client/login.php" class="nav-btn nav-btn-outline">Entrar</a>
            <a href="<?= $prefix ?>client/register.php" class="nav-btn nav-btn-solid">Cadastrar</a>
        <?php endif; ?>
    </div>
</nav>
