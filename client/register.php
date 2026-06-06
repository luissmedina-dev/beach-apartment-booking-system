<?php 

require_once("../config/connection.php");

$errors = [];

$role = "client";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe dados
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmpassword"];

    // Validacoes basicas
    if(strlen($password) < 8){
        $errors[] = "A senha deve conter pelo menos 8 caracteres";
    }

    if(strlen($name) < 3) {
        $errors[] = "O nome deve conter pelo menos 3 caracteres.";
    }

    if(strpos($name," ") === false) {
        $errors[] = "Digite seu nome completo";
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ 
        $errors[] = "Insira um E-mail válido!";
    }

    if($password != $confirmPassword) {
        $errors[] = "As senhas não coincidem.";
    }

    // Consulta o banco de dados
    if(empty($errors)){

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");

        $stmt->bindParam(":email", $email);

        $stmt->execute();

        $user = $stmt->fetch();

        if($user) {
            $errors[] = "Este email ja esta cadastrado";
        }

    if(empty($errors) && !$user) {      
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (
                                name, email, password, role
                                ) VALUES (
                                :name, :email, :password, :role
                                )");
        
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $passwordHash);
        $stmt->bindParam(":role", $role);
        $stmt->execute();

        echo "Cadastro realizado com sucesso";
    }   

    }

}


if(!empty($errors)) {
    
    foreach($errors as $error){
        echo $error . "<br>";
    }

}

?>

<div class="form-register">
    <form action="" method="post">
        <div class="register-input">
            <label for="name">Nome:</label>
            <input type="text" name="name" id="name" placeholder="Digite seu nome completo" required>
        </div>
        <div class="register-input">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" placeholder="Digite seu E-mail" required>
        </div>
        <div class="register-input">
            <label for="password">Senha:</label>
            <input type="password" name="password" id="password" placeholder="Digite sua senha" required>
        </div>
        <div class="register-input">
            <label for="confirmpassword">Confirmar senha:</label>
            <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirme sua senha" required>
        </div>
        <div class="register-btn">
            <input type="submit" value="Cadastrar">
        </div>
    </form>
</div>