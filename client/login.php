<?php

require_once("../config/connection.php");

$errors = [];

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim(filter_input(INPUT_POST, "email"));
    $password = filter_input(INPUT_POST, "password");

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");

    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        header("Location: dashboard.php");
        exit();

    } else {

        $errors[] = "Email ou senha inválidos.";

    }

}

?>

<?php if(!empty($errors)): ?>
    <div class="error-message">
        <?php foreach($errors as $error): ?>
            <p><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="login-container">
    <form action="" method="POST">
        <div class="login-input">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" placeholder="Digite seu email" required>
        </div>
        <div class="login-input">
            <label for="password">Senha:</label>
            <input type="password" name="password" id="password" placeholder="Digite sua senha" required>
        </div>
        <div class="login-btn">
            <input type="submit" value="Entrar">
        </div>
    </form>
</div>