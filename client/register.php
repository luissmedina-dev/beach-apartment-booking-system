<?php

require_once("../config/connection.php");

session_start();

$errors = [];
$success = false;
$role = "client";


if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $name            = trim(filter_input(INPUT_POST, "name"));
    $email           = trim(filter_input(INPUT_POST, "email"));
    $password        = filter_input(INPUT_POST, "password");
    $confirmPassword = filter_input(INPUT_POST, "confirmpassword");

    if(strlen($name) < 3) {
        $errors[] = "O nome deve ter pelo menos 3 caracteres.";
    }

    if(strpos($name, " ") === false) {
        $errors[] = "Digite seu nome completo.";
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Insira um e-mail válido.";
    }

    if(strlen($password) < 8) {
        $errors[] = "A senha deve ter pelo menos 8 caracteres.";
    }

    if($password !== $confirmPassword) {
        $errors[] = "As senhas não coincidem.";
    }

    if(empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $existingUser = $stmt->fetch();

        if($existingUser) {
            $errors[] = "Este e-mail já está cadastrado.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at, updated_at)
                                    VALUES (:name, :email, :password, :role, NOW(), NOW())");
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $passwordHash);
            $stmt->bindParam(":role", $role);
            $stmt->execute();
            $success = true;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro — Apartamento Bombinhas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="auth-page">

    <!-- Painel esquerdo: branding -->
    <div class="auth-brand">
        <a href="../public/index.php" class="auth-back-link">← Voltar ao site</a>

        <p class="auth-brand-logo">Bombinhas</p>
        <h1 class="auth-brand-title">Crie sua conta gratuita</h1>
        <p class="auth-brand-subtitle">
            Cadastre-se para solicitar reservas, acompanhar estadias e receber confirmações pelo nosso sistema.
        </p>
        <span class="auth-brand-tag">Bombinhas, SC</span>
    </div>

    <!-- Painel direito: formulário -->
    <div class="auth-form-panel">
        <div class="auth-form-inner">

            <div class="auth-form-header">
                <h2>Criar conta</h2>
                <p>Preencha os dados abaixo para se cadastrar</p>
            </div>

            <?php if(!empty($errors)): ?>
                <div class="auth-errors">
                    <?php foreach($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="auth-success">
                    <p>✓ Cadastro realizado com sucesso! <a href="login.php">Clique aqui para entrar.</a></p>
                </div>
            <?php endif; ?>

            <?php if(!$success): ?>
            <form action="" method="POST">
                <div class="auth-field">
                    <label for="name">Nome completo</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        placeholder="Seu nome completo"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                        required
                        autocomplete="name"
                    >
                </div>
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
                        placeholder="Mínimo 8 caracteres"
                        required
                        autocomplete="new-password"
                    >
                </div>
                <div class="auth-field">
                    <label for="confirmpassword">Confirmar senha</label>
                    <input
                        type="password"
                        name="confirmpassword"
                        id="confirmpassword"
                        placeholder="Repita a senha"
                        required
                        autocomplete="new-password"
                    >
                </div>
                <button type="submit" class="auth-btn">Criar conta</button>
            </form>
            <?php endif; ?>

            <p class="auth-alt-link">
                Já tem uma conta? <a href="login.php">Entrar</a>
            </p>

        </div>
    </div>

</div>

</body>
</html>
