<?php
// Preveri, ali se je PHP uspesno povezal na bazo (MariaDB)
$host = 'localhost:3007'; // Uporabite hostname definiran v docker-compose.yml
$user = 'root';
$password = 'root';
$db = 'onlinecodeeditor'; 

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