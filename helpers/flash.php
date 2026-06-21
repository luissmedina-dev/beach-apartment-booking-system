<?php 

function setFlash($type, $message){
    $_SESSION['flash'] = [
        "type" => $type,
        "message" => $message
    ];
}

function showFlash(){
    if(isset($_SESSION['flash'])){
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];

        echo "
            <div class='alert $type'>
                $message
            </div>
            ";

            unset($_SESSION['flash']);
    }
}

?>