<?php

require_once("../config/connection.php");
require_once("../dao/UserDAO.php");

$errors = [];
$userDAO = new UserDAO($conn);

session_start();

if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim(filter_input(INPUT_POST, "email"));
    $password = filter_input(INPUT_POST, "password");

    $user = $userDAO->findByEmailWithPassword($email);

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];


        if($user['role'] === "admin"){

            header("Location: ../admin/dashboard.php");
            exit();

        } else {

            header("Location: dashboard.php");
            exit();

        }

    } else {
        $errors[] = "E-mail ou senha inválidos.";
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — Apartamento Bombinhas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="auth-page">

    <!-- Painel esquerdo: branding -->
    <div class="auth-brand">
        <a href="../public/index.php" class="auth-back-link">← Voltar ao site</a>

        <p class="auth-brand-logo">Bombinhas</p>
        <h1 class="auth-brand-title">Seu refúgio à beira-mar</h1>
        <p class="auth-brand-subtitle">
            Acesse sua conta para consultar reservas e acompanhar o status das suas estadias.
        </p>
        <span class="auth-brand-tag">Bombinhas, SC</span>
    </div>

    <!-- Painel direito: formulário -->
    <div class="auth-form-panel">
        <div class="auth-form-inner">

            <div class="auth-form-header">
                <h2>Bem-vindo de volta</h2>
                <p>Entre com seu e-mail e senha para acessar</p>
            </div>

            <?php if(!empty($errors)): ?>
                <div class="auth-errors">
                    <?php foreach($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="auth-field">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="seu@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                </div>
                <div class="auth-field">
                    <label for="password">Senha</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Sua senha"
                        required
                        autocomplete="current-password"
                    >
                </div>
                <button type="submit" class="auth-btn">Entrar</button>
            </form>

            <p class="auth-alt-link">
                Ainda não tem conta? <a href="register.php">Cadastre-se</a>
            </p>

        </div>
    </div>

</div>

</body>
</html>
