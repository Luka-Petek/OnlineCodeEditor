<?php
session_start();
require_once 'db.php'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header('Location: ../prijava.html');
    exit;
}

$fk_uporabnik = $_SESSION['user_id'];
$db = getDatabaseConnection();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ime_projekta = trim($_POST['imeProjekta'] ?? '');
    $opis = trim($_POST['opis'] ?? '');
    $jezik = trim($_POST['jezik'] ?? '');
    
    $napake = [];

    if (empty($ime_projekta)) {
        $napake[] = 'Ime projekta je obvezno.';
    }
    if (strlen($ime_projekta) > 255) {
        $napake[] = 'Ime projekta je predolgo.';
    }

    if (empty($napake)) {

        try {
            $sql = "INSERT INTO projekt (imeProjekta, opis, jezik, FKuporabnik) 
                    VALUES (:imeProjekta, :opis, :jezik, :fk_uporabnik)";
            
            $stmt = $db->prepare($sql);
            
            $stmt->bindParam(':imeProjekta', $ime_projekta, PDO::PARAM_STR);
            $stmt->bindParam(':opis', $opis, PDO::PARAM_STR);
            $stmt->bindParam(':jezik', $jezik, PDO::PARAM_STR);
            $stmt->bindParam(':fk_uporabnik', $fk_uporabnik, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $nov_projekt_id = $db->lastInsertId();
            
            header("Location: ../dashboard.html?id=" . $nov_projekt_id);
            exit;

        } 
        catch (PDOException $e) {
            if ($e->getCode() == '23000') { 
                $napake[] = "Projekt z imenom '$ime_projekta' za vaš račun že obstaja. Prosimo, izberite drugo ime.";
            } else {
                $napake[] = "Napaka pri shranjevanju projekta: " . $e->getMessage();
            }
        }
    }
    
    // Ob napaki
    if (!empty($napake)) {
        $_SESSION['error_message'] = implode('<br>', $napake);
        
        $redirect_url = "../createProjectHTML.php?jezik=" . urlencode($jezik);
        header("Location: " . $redirect_url);
        exit;
    }

} 
else {
    header('Location: ../index.php');
    exit;
}
?>