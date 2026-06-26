<?php 

class UserDAO {

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findByEmail($email){

        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");

        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function createUser($name, $email, $password, $role){

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO users
                                        (name, email, password, role, created_at, updated_at)
                                    VALUES 
                                        (:name, :email, :password, :role, NOW(), NOW()
                                    ");
        
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);

        return $stmt->execute();

    }

    public function findByEmailWithPassword($email){

        $stmt = $this->conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = :email");

        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

}