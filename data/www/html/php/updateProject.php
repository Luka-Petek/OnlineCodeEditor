<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header('Location: ../prijava.html');
    exit;
}

$fk_uporabnik = $_SESSION['user_id'];
$db = getDatabaseConnection(); 
$napake = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $projekt_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$projekt_id) {
        $_SESSION['error_message'] = 'Neveljaven ID projekta za urejanje.';
        header('Location: ../dashboard.php');
        exit;
    }

    try {
        $sql = "SELECT id, imeProjekta, opis, jezik FROM projekt WHERE id = :projekt_id AND FKuporabnik = :fk_uporabnik";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':projekt_id', $projekt_id, PDO::PARAM_INT);
        $stmt->bindParam(':fk_uporabnik', $fk_uporabnik, PDO::PARAM_INT);
        $stmt->execute();
        
        $projekt = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$projekt) {
            $_SESSION['error_message'] = 'Projekt ni najden ali nimate dovoljenja za urejanje.';
            header('Location: ../dashboard.php');
            exit;
        }

        $_SESSION['edit_data'] = $projekt;

        header("Location: ../updateProjectHTML.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Napaka pri branju projekta: " . $e->getMessage();
        header('Location: ../dashboard.php');
        exit;
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $projekt_id = filter_input(INPUT_POST, 'projektId', FILTER_VALIDATE_INT);
    
    if (!$projekt_id) {
        $napake[] = 'Manjka ID projekta za posodobitev.';
    }

    $ime_projekta = trim($_POST['imeProjekta'] ?? '');
    $opis = trim($_POST['opis'] ?? '');
    $jezik = trim($_POST['jezik'] ?? '');

    if (empty($ime_projekta)) {
        $napake[] = 'Ime projekta je obvezno.';
    }
    if (strlen($ime_projekta) > 255) {
        $napake[] = 'Ime projekta je predolgo.';
    }
    
    if (empty($napake)) {
        try {
            $sql_check = "SELECT id FROM projekt WHERE imeProjekta = :imeProjekta AND FKuporabnik = :fk_uporabnik AND id != :projekt_id";
            $stmt_check = $db->prepare($sql_check);
            $stmt_check->bindParam(':imeProjekta', $ime_projekta, PDO::PARAM_STR);
            $stmt_check->bindParam(':fk_uporabnik', $fk_uporabnik, PDO::PARAM_INT);
            $stmt_check->bindParam(':projekt_id', $projekt_id, PDO::PARAM_INT);
            $stmt_check->execute();
            if ($stmt_check->rowCount() > 0) {
                 $napake[] = "Projekt z imenom '$ime_projekta' za vaš račun že obstaja. Prosimo, izberite drugo ime.";
            }
        } catch (PDOException $e) {
            $napake[] = "Napaka pri preverjanju unikatnosti imena: " . $e->getMessage();
        }
    }

    if (empty($napake)) {
        try {
            $sql = "UPDATE projekt SET imeProjekta = :imeProjekta, opis = :opis, jezik = :jezik, datumNastanka = NOW()
                    WHERE id = :projekt_id AND FKuporabnik = :fk_uporabnik";
            
            $stmt = $db->prepare($sql);
            
            $stmt->bindParam(':imeProjekta', $ime_projekta, PDO::PARAM_STR);
            $stmt->bindParam(':opis', $opis, PDO::PARAM_STR);
            $stmt->bindParam(':jezik', $jezik, PDO::PARAM_STR);
            $stmt->bindParam(':projekt_id', $projekt_id, PDO::PARAM_INT);
            $stmt->bindParam(':fk_uporabnik', $fk_uporabnik, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $_SESSION['success_message'] = "Projekt '$ime_projekta' je bil uspešno posodobljen.";
            header("Location: ../dashboard.php");
            exit;

        } catch (PDOException $e) {
            $napake[] = "Napaka pri posodabljanju projekta: " . $e->getMessage();
        }
    }

    if (!empty($napake)) {
        $_SESSION['error_message'] = implode('<br>', $napake);
        
        $_SESSION['form_data'] = [
            'imeProjekta' => $ime_projekta,
            'opis' => $opis,
            'jezik' => $jezik
        ];

        $redirect_url = "../updateProjectHTML.php?id=" . $projekt_id;
        header("Location: " . $redirect_url);
        exit;
    }

} else {
    header('Location: ../dashboard.php');
    exit;
}
?>