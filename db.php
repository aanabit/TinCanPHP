<?php

$host = 'localhost';
$db = 'quizez';
$db_user = 'root';
$db_pass = 'root';

function db_connect() {
    try {
        $conn = new PDO("mysql:host=".$host.";dbname=".$db, $db_user, $db_pass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ($conn);
    }
    catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}

function db_getQuestions($conn) {
    $sql = "SELECT * FROM questions";
    $result = $conn->query($sql);
    return $result;
}

function db_getQuestion($id, $conn) {
    $sql = "SELECT * FROM questions WHERE id = ".$id;
    $result = $conn->query($sql);
    return $result;
}

function db_insertQuestion($data, $conn) {
    $sql = "INSERT INTO questions('type', 'before_text', 'after_text', 'values_text') VALUES(''".$data['type']."', '".$data['before_text']."', '".$data['after_text']."', '".$data['values_text']."')";
    $conn->query($sql);
}