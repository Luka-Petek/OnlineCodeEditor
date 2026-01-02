<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: ../updateProfileHTML.php");
    exit;
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../prijava.html");
    exit;
}

$db = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

$ime = trim($_POST['ime']);
$priimek = trim($_POST['priimek']);
$gmail = trim($_POST['gmail']);

$_SESSION['form_data'] = [
    'ime' => $ime,
    'priimek' => $priimek,
    'gmail' => $gmail
];

if (empty($ime) || empty($priimek) || empty($gmail)) {
    $_SESSION['error_message'] = "Vsa polja morajo biti izpolnjena.";
    header("location: ../updateProfileHTML.php");
    exit;
}

if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Vnesite veljaven e-poštni naslov.";
    header("location: ../updateProfileHTML.php");
    exit;
}

try {
    $sql = "UPDATE uporabnik SET ime = :ime, priimek = :priimek, gmail = :gmail WHERE id = :id";
    $stmt = $db->prepare($sql);
    
    $stmt->bindParam(':ime', $ime);
    $stmt->bindParam(':priimek', $priimek);
    $stmt->bindParam(':gmail', $gmail);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['user_data']['ime'] = $ime;
        $_SESSION['user_data']['priimek'] = $priimek;
        $_SESSION['user_data']['gmail'] = $gmail;
        
        $_SESSION['success_message'] = "Podatki so bili uspešno posodobljeni.";
        unset($_SESSION['form_data']); 
    } else {
        $_SESSION['error_message'] = "Napaka pri izvajanju poizvedbe.";
    }

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        $_SESSION['error_message'] = "Ta e-poštni naslov že uporablja drug uporabnik.";
    } else {
        $_SESSION['error_message'] = "Napaka sistema: " . $e->getMessage();
    }
}

header("location: ../updateProfileHTML.php");
exit;