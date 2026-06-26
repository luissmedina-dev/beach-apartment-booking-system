<?php 

class User {

    private $id;
    private $name;
    private $email;
    private $password;
    private $role;

    public function __construct($name, $email, $password, $role){

        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;

    }

    public function getName(){
        return $this->name;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getRole(){
        return $this->role;
    }

}