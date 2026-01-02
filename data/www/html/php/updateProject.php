<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header('Location: ../prijava.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$db = getDatabaseConnection(); 
$napake = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $sql = "SELECT id, ime, priimek, gmail FROM uporabnik WHERE id = :user_id";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $uporabnik = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$uporabnik) {
            $_SESSION['error_message'] = 'Uporabnik ni najden.';
            header('Location: ../dashboard.php');
            exit;
        }

        $_SESSION['edit_profile_data'] = $uporabnik;
        header("Location: ../updateProfileHTML.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Napaka pri branju profila: " . $e->getMessage();
        header('Location: ../dashboard.php');
        exit;
    }

// POST METODA: Shranjevanje sprememb
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $ime = trim($_POST['ime'] ?? '');
    $priimek = trim($_POST['priimek'] ?? '');
    $gmail = trim($_POST['gmail'] ?? '');

    if (empty($ime)) {
        $napake[] = 'Ime je obvezno.';
    }
    if (empty($priimek)) {
        $napake[] = 'Priimek je obvezen.';
    }
    if (empty($gmail)) {
        $napake[] = 'E-pošta je obvezna.';
    } elseif (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        $napake[] = 'Vnesite veljaven e-poštni naslov.';
    }
    
    // Preverjanje, če e-pošta že obstaja pri drugem uporabniku
    if (empty($napake)) {
        try {
            $sql_check = "SELECT id FROM uporabnik WHERE gmail = :gmail AND id != :user_id";
            $stmt_check = $db->prepare($sql_check);
            $stmt_check->bindParam(':gmail', $gmail, PDO::PARAM_STR);
            $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                 $napake[] = "E-poštni naslov '$gmail' že uporablja drug račun.";
            }
        } catch (PDOException $e) {
            $napake[] = "Napaka pri preverjanju e-pošte: " . $e->getMessage();
        }
    }

    // Če ni napak, posodobimo bazo
    if (empty($napake)) {
        try {
            $sql = "UPDATE uporabnik SET ime = :ime, priimek = :priimek, gmail = :gmail 
                    WHERE id = :user_id";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':ime', $ime, PDO::PARAM_STR);
            $stmt->bindParam(':priimek', $priimek, PDO::PARAM_STR);
            $stmt->bindParam(':gmail', $gmail, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            
            $stmt->execute();
            
            // Osvežimo sejne podatke, če jih uporabljaš v meniju
            $_SESSION['ime'] = $ime;
            $_SESSION['priimek'] = $priimek;
            
            $_SESSION['success_message'] = "Profil je bil uspešno posodobljen.";
            header("Location: ../dashboard.php");
            exit;

        } catch (PDOException $e) {
            $napake[] = "Napaka pri posodabljanju profila: " . $e->getMessage();
        }
    }

    // Če so napake, vrnemo na obrazec
    if (!empty($napake)) {
        $_SESSION['error_message'] = implode('<br>', $napake);
        
        $_SESSION['form_data'] = [
            'ime' => $ime,
            'priimek' => $priimek,
            'gmail' => $gmail
        ];

        header("Location: ../updateProfileHTML.php");
        exit;
    }

} else {
    header('Location: ../dashboard.php');
    exit;
}
?>