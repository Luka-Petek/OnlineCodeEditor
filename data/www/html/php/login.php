<?php
// Zaženi sejo (session) za shranjevanje uporabniških podatkov
session_start();

// Preveri, če je uporabnik že prijavljen
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.html"); // Preusmeri na nadzorno ploščo
    exit;
}

require_once 'db.php'; // Uporabimo že definiran PDO priključek

// Inicializacija spremenljivk
$gmail = $geslo = "";
$gmail_err = $geslo_err = $login_err = "";

// Preveri, če je obrazec oddan
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Validacija e-pošte in gesla
    if (empty(trim($_POST["gmail"]))) {
        $gmail_err = "Prosimo, vnesite e-poštni naslov.";
    } else {
        $gmail = trim($_POST["gmail"]);
    }

    if (empty(trim($_POST["geslo"]))) {
        $geslo_err = "Prosimo, vnesite geslo.";
    } else {
        $geslo = trim($_POST["geslo"]);
    }

    // 2. Preverjanje podatkov
    if (empty($gmail_err) && empty($geslo_err)) {
        try {
            $pdo = getDatabaseConnection();
            
            // Pripravi SQL izjavo za pridobitev gesla shranjenega uporabnika
            $sql = "SELECT id, ime, geslo FROM uporabnik WHERE gmail = :gmail";
            $stmt = $pdo->prepare($sql);
            
            // Izvrši poizvedbo
            $stmt->execute(['gmail' => $gmail]);

            if ($stmt->rowCount() == 1) {
                // E-pošta obstaja, pridobi podatke
                $row = $stmt->fetch();
                $hashed_password = $row['geslo'];
                
                // Preveri geslo
                if (password_verify($geslo, $hashed_password)) {
                    // Geslo je pravilno, seveda ustvari sejo!
                    
                    // Nastavi spremenljivke seje
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $row['id'];
                    $_SESSION["ime"] = $row['ime'];
                    
                    // Preusmeri uporabnika na varno stran
                    header("location: ../index.html"); 
                    exit;

                } else {
                    // Geslo ni pravilno
                    $login_err = "Napačno geslo ali e-poštni naslov.";
                }
            } else {
                // Uporabnik z dano e-pošto ne obstaja
                $login_err = "Napačno geslo ali e-poštni naslov.";
            }

        } catch (\PDOException $e) {
             $login_err = "Napaka pri povezavi z bazo. Prosimo, poskusite znova.";
        }
    }

    // Če prijava ne uspe, prikaži napako
    if (!empty($login_err)) {
        echo "<!DOCTYPE html><html lang='sl'><head><title>Napaka</title><style>body{font-family: sans-serif; background: #1f2937; color: white; padding: 20px;} h2{color:#f87171;}</style></head><body>";
        echo "<h2>Napaka pri prijavi</h2>";
        echo "<p style='color: #f87171;'>" . htmlspecialchars($login_err) . "</p>";
        
        // Prikaz morebitnih napak pri validaciji
        if (!empty($gmail_err)) echo "<p style='color: #f87171;'>E-pošta: " . $gmail_err . "</p>";
        if (!empty($geslo_err)) echo "<p style='color: #f87171;'>Geslo: " . $geslo_err . "</p>";
        
        echo "<p><a style='color:#3b82f6;' href='../html/prijava.html'>Poskusi znova</a></p>";
        echo "</body></html>";
    }
} else {
    // Če stran pokličemo direktno, preusmerimo na obrazec
    header("location: ../prijava.html");
    exit();
}
?>