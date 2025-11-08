<?php
// Zaženi sejo (session) za shranjevanje uporabniških podatkov
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}

require_once 'db.php';

$gmail = $geslo = "";
$gmail_err = $geslo_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

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

    if (empty($gmail_err) && empty($geslo_err)) {
        try {
            $pdo = getDatabaseConnection();

            $sql = "SELECT id, ime, geslo FROM uporabnik WHERE gmail = :gmail";
            $stmt = $pdo->prepare($sql);

            $stmt->execute(['gmail' => $gmail]);

            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch();
                $hashed_password = $row['geslo'];

                // Preveri geslo
                if (password_verify($geslo, $hashed_password)) {

                    $_SESSION["loggedin"] = true;
                    $_SESSION["user_id"] = $row['id'];
                    $_SESSION["ime"] = $row['ime'];

                    header("location: ../index.php");
                    exit;

                } else {
                    $login_err = "Napačno geslo ali e-poštni naslov.";
                }
            } else {
                $login_err = "Napačno geslo ali e-poštni naslov.";
            }

        } catch (\PDOException $e) {
            $login_err = "Napaka pri povezavi z bazo. Prosimo, poskusite znova.";
        }
    }

    if (!empty($login_err)) {
        echo "<!DOCTYPE html><html lang='sl'><head><title>Napaka</title><style>body{font-family: sans-serif; background: #1f2937; color: white; padding: 20px;} h2{color:#f87171;}</style></head><body>";
        echo "<h2>Napaka pri prijavi</h2>";
        echo "<p style='color: #f87171;'>" . htmlspecialchars($login_err) . "</p>";

        // Prikaz morebitnih napak pri validaciji
        if (!empty($gmail_err))
            echo "<p style='color: #f87171;'>E-pošta: " . $gmail_err . "</p>";
        if (!empty($geslo_err))
            echo "<p style='color: #f87171;'>Geslo: " . $geslo_err . "</p>";

        echo "<p><a style='color:#3b82f6;' href='../html/prijava.html'>Poskusi znova</a></p>";
        echo "</body></html>";
    }
} else {
    // Če stran pokličemo direktno, preusmerimo na obrazec
    header("location: ../prijava.html");
    exit();
}
?>