<?php

require_once 'db.php';

$ime = $priimek = $gmail = $geslo = $geslo_ponovi = "";
$ime_err = $priimek_err = $gmail_err = $geslo_err = $geslo_ponovi_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["ime"]))) {
        $ime_err = "Prosimo, vnesite ime.";
    } else {
        $ime = trim($_POST["ime"]);
    }

    if (empty(trim($_POST["priimek"]))) {
        $priimek_err = "Prosimo, vnesite priimek.";
    } else {
        $priimek = trim($_POST["priimek"]);
    }

    if (empty(trim($_POST["geslo"]))) {
        $geslo_err = "Prosimo, vnesite geslo.";
    } elseif (strlen(trim($_POST["geslo"])) < 8) {
        $geslo_err = "Geslo mora vsebovati vsaj 8 znakov.";
    } else {
        $geslo = trim($_POST["geslo"]);
    }

    if (empty(trim($_POST["geslo_ponovi"]))) {
        $geslo_ponovi_err = "Prosimo, potrdite geslo.";
    } 
    else {
        $geslo_ponovi = trim($_POST["geslo_ponovi"]);
        if (empty($geslo_err) && ($geslo != $geslo_ponovi)) {
            $geslo_ponovi_err = "Gesli se ne ujemata.";
        }
    }

    if (empty(trim($_POST["gmail"]))) {
        $gmail_err = "Prosimo, vnesite e-poštni naslov.";
    } else {
        $gmail = trim($_POST["gmail"]);

        try {
            $pdo = getDatabaseConnection();
            
            // Pripravi izjavo za preverjanje, če e-poštni naslov že obstaja
            $sql = "SELECT id FROM uporabnik WHERE gmail = :gmail";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['gmail' => $gmail]);

            if ($stmt->rowCount() > 0) {
                $gmail_err = "Ta e-poštni naslov je že registriran.";
            }

        } catch (\PDOException $e) {
             $gmail_err = "Sistemska napaka pri preverjanju uporabnika. (" . $e->getMessage() . ")";
        }
    }
    
    $error_messages = array_filter([$ime_err, $priimek_err, $gmail_err, $geslo_err, $geslo_ponovi_err]);

    if (empty($error_messages)) {
        
        try {
            $pdo = getDatabaseConnection();
            $sql = "INSERT INTO uporabnik (ime, priimek, gmail, geslo) VALUES (:ime, :priimek, :gmail, :geslo)";
            $stmt = $pdo->prepare($sql);
            
            // Šifriranje gesla za varno shranjevanje
            $param_geslo = password_hash($geslo, PASSWORD_DEFAULT); 

            $stmt->execute([
                'ime' => $ime,
                'priimek' => $priimek,
                'gmail' => $gmail,
                'geslo' => $param_geslo
            ]);
            
            header("location: ../prijava.html");
            exit();

        } catch (\PDOException $e) {
            // Če se vstavljanje v bazo ne posreči
            $final_error = "Nekaj je šlo narobe. Prosimo, poskusite znova pozneje. (SQL napaka: " . $e->getMessage() . ")";
             echo "<!DOCTYPE html><html lang='sl'><head><title>Napaka</title><style>body{font-family: sans-serif; background: #1f2937; color: white; padding: 20px;} h2{color:#f87171;}</style></head><body>";
             echo "<h2>Napaka pri registraciji</h2>";
             echo "<p style='color: #f87171;'>Sistemska napaka: " . htmlspecialchars($final_error) . "</p>";
             echo "<p><a style='color:#3b82f6;' href='../html/registracija.html'>Poskusi znova</a></p>";
             echo "</body></html>";
        }
    } else {
        // Prikaz vseh napak pri validaciji
        echo "<!DOCTYPE html><html lang='sl'><head><title>Napaka</title><style>body{font-family: sans-serif; background: #1f2937; color: white; padding: 20px;} h2{color:#f87171;}</style></head><body>";
        echo "<h2>Napaka pri registraciji</h2>";
        if (!empty($ime_err)) echo "<p style='color: #f87171;'>Ime: " . $ime_err . "</p>";
        if (!empty($priimek_err)) echo "<p style='color: #f87171;'>Priimek: " . $priimek_err . "</p>";
        if (!empty($gmail_err)) echo "<p style='color: #f87171;'>E-pošta: " . $gmail_err . "</p>";
        if (!empty($geslo_err)) echo "<p style='color: #f87171;'>Geslo: " . $geslo_err . "</p>";
        if (!empty($geslo_ponovi_err)) echo "<p style='color: #f87171;'>Potrditev gesla: " . $geslo_ponovi_err . "</p>";
        
        echo "<p><a style='color:#3b82f6;' href='../html/registracija.html'>Poskusi znova</a></p>";
        echo "</body></html>";
    }

} else {
    // Če stran pokličemo direktno, preusmerimo na obrazec
    header("location: ../registracija.html");
    exit();
}
?>