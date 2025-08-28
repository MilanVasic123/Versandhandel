<?php
    $servername = "localhost";
    $user = "root";
    $pw = "";
    $db = "produkte";
    
    try{
        $con = new mysqli($servername, $user, $pw,  $db);
    }catch(mysqli_sql_exception $e){
        die("Fehler beim Verbinden -> ".$e->getMessage());
    }
?>