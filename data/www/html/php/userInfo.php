<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../prijava.html");
    exit;
}

require_once 'db.php';
$ime = $priimek = $gmail = "";

try {
    $pdo = getDatabaseConnection();

    $sql = "SELECT ime, priimek, gmail FROM uporabnik WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $_SESSION["user_id"]]);
    
    if ($row = $stmt->fetch()) {
        $ime = $row['ime'];
        $priimek = $row['priimek'];
        $gmail = $row['gmail'];
    } else {
        header("location: logout.php");
        exit;
    }

} catch (\PDOException $e) {
    $error = "neki je narobe";
}
?>