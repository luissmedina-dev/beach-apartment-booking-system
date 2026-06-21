<?php
// Garante sessão ativa antes de qualquer verificação de admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nome da página atual, usado para destacar o item ativo na sidebar
$adminCurrentPage = basename($_SERVER['PHP_SELF']);

function adminNavActive($page, $current) {
    return $page === $current ? 'admin-nav-active' : '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <title>Painel Administrativo — Beach Apartment</title>
</head>
<body class="admin-body">

<div class="admin-shell">

    <!-- ════════════════ SIDEBAR ════════════════ -->
    <aside class="admin-sidebar">

        <div class="admin-brand">
            <div class="admin-brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 17C5 15 7 15 9 17C11 19 13 19 15 17C17 15 19 15 21 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M3 12C5 10 7 10 9 12C11 14 13 14 15 12C17 10 19 10 21 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    <circle cx="17" cy="6" r="3" fill="currentColor"/>
                </svg>
            </div>
            <div class="admin-brand-text">
                <span class="admin-brand-name">Beach Apartment</span>
                <span class="admin-brand-tag">Painel administrativo</span>
            </div>
        </div>

        <nav class="admin-nav">
            <span class="admin-nav-section">Visão geral</span>
            <a href="dashboard.php" class="<?= adminNavActive('dashboard.php', $adminCurrentPage) ?>">
                <span class="admin-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="9" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="3" width="7" height="5" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="12" width="7" height="9" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="3" y="16" width="7" height="5" rx="1.5" stroke="currentColor" stroke-width="1.8"/></svg>
                </span>
                Dashboard
            </a>
            <a href="reservations.php" class="<?= adminNavActive('reservations.php', $adminCurrentPage) ?>">
                <span class="admin-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3 10H21" stroke="currentColor" stroke-width="1.8"/><path d="M8 3V7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M16 3V7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                Reservas
            </a>

            <span class="admin-nav-section">Gestão</span>
            <a href="users.php" class="<?= adminNavActive('users.php', $adminCurrentPage) ?>">
                <span class="admin-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 20C4.5 16.5 6.5 15 9 15C11.5 15 13.5 16.5 14.5 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M15.5 6.5C16.9 6.9 18 8.1 18 9.7C18 11.1 17.2 12.2 16 12.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M16.5 15.2C18.6 15.8 20 17.3 20.7 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                Usuários
            </a>
            <a href="expanses.php" class="<?= adminNavActive('expanses.php', $adminCurrentPage) ?>">
                <span class="admin-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7V17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M14.8 9.5C14.8 8.4 13.6 7.5 12 7.5C10.4 7.5 9.2 8.4 9.2 9.6C9.2 12 14.8 10.8 14.8 13.2C14.8 14.5 13.6 15.5 12 15.5C10.4 15.5 9.2 14.6 9.2 13.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                Despesas
            </a>

            <span class="admin-nav-section">Inteligência</span>
            <a href="reports.php" class="<?= adminNavActive('reports.php', $adminCurrentPage) ?>">
                <span class="admin-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 20V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M10 20V8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M16 20V11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M21 5L15 11L11 8L4 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                Relatórios
            </a>
            <a href="settings.php" class="<?= adminNavActive('settings.php', $adminCurrentPage) ?>">
                <span class="admin-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M19.4 13.5C19.5 13 19.5 12.5 19.5 12C19.5 11.5 19.5 11 19.4 10.5L21.4 9L19.9 6.3L17.6 7.2C16.9 6.6 16.1 6.1 15.2 5.8L14.8 3.4H12.2L11.8 5.8C10.9 6.1 10.1 6.6 9.4 7.2L7.1 6.3L5.6 9L7.6 10.5C7.5 11 7.5 11.5 7.5 12C7.5 12.5 7.5 13 7.6 13.5L5.6 15L7.1 17.7L9.4 16.8C10.1 17.4 10.9 17.9 11.8 18.2L12.2 20.6H14.8L15.2 18.2C16.1 17.9 16.9 17.4 17.6 16.8L19.9 17.7L21.4 15L19.4 13.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                </span>
                Configurações
            </a>
        </nav>

        <div class="admin-sidebar-footer">
            <a href="../client/logout.php" class="admin-logout-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 17L20 12L15 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 12H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M9 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Sair do painel
            </a>
        </div>
    </aside>

    <!-- ════════════════ ÁREA DE CONTEÚDO ════════════════ -->
    <div class="admin-main">

        <header class="admin-topbar">
            <div class="admin-topbar-title" id="adminTopbarTitle"></div>
            <div class="admin-topbar-user">
                <?php
                    $adminName = $_SESSION['user_name'] ?? 'Administrador';
                    $initials  = strtoupper(substr($adminName, 0, 1));
                ?>
                <div class="admin-user-avatar"><?= $initials ?></div>
                <div class="admin-user-info">
                    <span class="admin-user-name"><?= htmlspecialchars($adminName) ?></span>
                    <span class="admin-user-role">Proprietário</span>
                </div>
            </div>
        </header>

        <main class="admin-content">
