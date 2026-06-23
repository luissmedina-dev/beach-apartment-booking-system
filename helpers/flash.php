<?php 

function setFlash($type, $message){
    $_SESSION['flash'] = [
        "type" => $type,
        "message" => $message
    ];
}

function showFlash(){
    if(isset($_SESSION['flash'])){
        $type    = htmlspecialchars($_SESSION['flash']['type'],    ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8');

        echo "
            <div class='alert {$type}'>
                {$message}
            </div>
            ";

        unset($_SESSION['flash']);
    }
}

?>