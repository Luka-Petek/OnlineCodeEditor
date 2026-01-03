<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: ../updatePasswordHTML.php");
    exit;
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../prijava.html");
    exit;
}

$db = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

//ce je prazno
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['error_message'] = "Vsa polja morajo biti izpolnjena.";
    header("location: ../updatePasswordHTML.php");
    exit;
}

//če se novi gesli ujemata
if ($new_password !== $confirm_password) {
    $_SESSION['error_message'] = "Novo geslo in potrditev se ne ujemata.";
    header("location: ../updatePasswordHTML.php");
    exit;
}

//dolžina gesla
if (strlen($new_password) < 8) {
    $_SESSION['error_message'] = "Novo geslo mora imeti vsaj 8 znakov.";
    header("location: ../updatePasswordHTML.php");
    exit;
}

try {
    $stmt = $db->prepare("SELECT geslo FROM uporabnik WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($current_password, $user['geslo'])) {
        // 5. Trenutno geslo je pravilno, posodobimo na novo
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update_stmt = $db->prepare("UPDATE uporabnik SET geslo = :geslo WHERE id = :id");
        $update_stmt->bindParam(':geslo', $new_hashed_password);
        $update_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Geslo je bilo uspešno spremenjeno.";
        } else {
            $_SESSION['error_message'] = "Prišlo je do napake pri posodabljanju baze.";
        }
    } else {
        $_SESSION['error_message'] = "Trenutno geslo ni pravilno.";
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = "Napaka sistema: " . $e->getMessage();
}

header("location: ../updatePasswordHTML.php");
exit;