<?php
    $conn = [
        "host" => "localhost",
        "user" => "root",
        "pass" => "root",
        "db" => "comilandia",
        "driver" => "mysql"
    ];
    
    $db = null;
    try{
        $connect = sprintf('%s:host=%s;dbname=%s', $conn['driver'],$conn['host'],$conn['db']);
        $db = new PDO($connect,$conn['user'],$conn['pass']);
        $db->exec("set names utf8");
    }catch(PDOException $exception){
        echo "Connection error: " . $exception->getMessage();
    }
