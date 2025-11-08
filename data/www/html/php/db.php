<?php
function getDatabaseConnection() {
    $host = 'podatkovna-baza';
    $user = 'root';
    $password = 'root';
    $db = 'onlinecodeeditor'; 
    $port = 3306;

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $password, $options);
        return $pdo;
    } catch (\PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        die("Napaka: Ne morem se povezati z bazo podatkov. Prosim, poskusite kasneje.");
    }
}

function checkDatabaseStatus() {
    try {
        $pdo = getDatabaseConnection();
        $pdo->query('SELECT 1');
        return 'Povezava z MariaDB (podatkovna-baza) je USPEŠNA! 🎉';
    } catch (\Exception $e) {
        return 'Napaka pri povezovanju z MariaDB: ' . $e->getMessage();
    }
}