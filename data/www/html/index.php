<?php
// Preveri, ali se je PHP uspesno povezal na bazo (MariaDB)
$host = 'localhost:3007'; // Uporabite hostname definiran v docker-compose.yml
$user = 'root';
$password = 'root';
$db = 'mysql'; 

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $password, $options);
    $db_status = 'Povezava z MariaDB (podatkovna-baza) je USPEŠNA! 🎉';
} catch (\PDOException $e) {
    $db_status = 'Napaka pri povezovanju z MariaDB: ' . $e->getMessage();
}

// Preveri, ali so dovoljenja za datotečni sistem OK (za File Explorer)
$file_status = 'Pot do uporabniških datotek (/var/www/html/user_files) je dosegljiva in ima dovoljenje za pisanje.';
if (!is_writable('user_files')) {
    $file_status = 'NAPAKA: Mapa /var/www/html/user_files nima dovoljenj za pisanje. Preverite dovoljenja na vašem hostu.';
}

?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Online Code Editor - Projekt</title>
</head>
<body>
    <h1>Online Code Editor - Arhitektura uspešno postavljena!</h1>
    
    <h2>Stanje storitev</h2>
    <ul>
        <li>**Spletni strežnik (PHP/Apache):** TEČE (Trenutno si ogledujete to stran na portu 8000).</li>
        <li>**Podatkovna baza (MariaDB):** <p style="color: green; font-weight: bold;"><?= $db_status ?></p>
        </li>
        <li>**Execution API Service (Node.js):** Zahteva dodatni test, vendar vsebnik teče.</li>
    </ul>

</body>
</html>