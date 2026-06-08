<?php
session_start();
require_once '../src/config.php';
$con = conexion();

if(isset($_POST['action']) && $_POST['action'] == 'login'){
    $user = $con->real_escape_string($_POST['username']);
    $pass = $_POST['password'];

    $query = $con->query("SELECT * FROM admin WHERE username = '$user' LIMIT 1");
    
    if($query && $query->num_rows > 0){
        $row = $query->fetch_assoc();
        if(password_verify($pass, $row['password'])){
            $_SESSION['admin'] = true;
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'logout'){
    session_destroy();
    echo "success";
}
?>
