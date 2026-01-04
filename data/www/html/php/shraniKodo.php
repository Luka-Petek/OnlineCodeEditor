<?php
session_start();
require_once 'db.php'; 

header('Content-Type: application/json');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Napaka pri avtentikaciji ali neveljavna metoda.']);
    exit;
}

//input je recimo $input: {"projektId": 123, "koda": "print('Hello')", "jezik": "python"}
//to je php stream
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$projekt_id = $data['projektId'] ?? null;
$koda = $data['koda'] ?? null;
$jezik = $data['jezik'] ?? null;
$fk_uporabnik = $_SESSION['user_id'];

if (!$projekt_id || !is_numeric($projekt_id) || $koda === null || !$jezik) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Manjkajoči podatki za shranjevanje.']);
    exit;
}

try {
    $db = getDatabaseConnection();

    $ekstenzija = match (strtolower($jezik)) {
        'java' => 'java',
        'python' => 'py',
        'c' => 'c',
        'javascript' => 'js',
        'html' => 'html',
        default => 'txt',
    };
    $imeDatoteke = "Main." . $ekstenzija; 
    
    $dir_osnova = '/var/www/html/user_files/'; 
    $dir_path = $dir_osnova . $fk_uporabnik . '/' . $projekt_id . '/';
    $full_file_path = $dir_path . $imeDatoteke;
    
    
    //Preverjanje lastništva projekta (Varnost)
    $sql_check_owner = "SELECT FKuporabnik FROM projekt WHERE id = :projekt_id";
    $stmt_owner = $db->prepare($sql_check_owner);
    $stmt_owner->bindParam(':projekt_id', $projekt_id, PDO::PARAM_INT);
    $stmt_owner->execute();
    $owner_id = $stmt_owner->fetchColumn();

    //da je projekt dejansko od prijavljenega uporabnika
    if ($owner_id != $fk_uporabnik) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Dostop zavrnjen. Niste lastnik projekta.']);
        exit;
    }
    
    //Fizično shranjevanje kode
    if (!is_dir($dir_path)) {
        if (!mkdir($dir_path, 0777, true) && !is_dir($dir_path)) {
             throw new \RuntimeException(sprintf('Mapa "%s" ni mogla biti ustvarjena', $dir_path));
        }
    }
    if (file_put_contents($full_file_path, $koda) === false) {
        throw new \Exception("Napaka pri pisanju datoteke na disk. Preverite pravice mape.");
    }
    
    $sql_check_file = "SELECT id FROM datoteka WHERE FKprojekt = :projekt_id AND imeDatoteke = :imeDatoteke";
    $stmt_check_file = $db->prepare($sql_check_file);
    $stmt_check_file->bindParam(':projekt_id', $projekt_id, PDO::PARAM_INT);
    $stmt_check_file->bindParam(':imeDatoteke', $imeDatoteke);
    $stmt_check_file->execute();
    $datoteka_id = $stmt_check_file->fetchColumn();

    // Posodobitev ali vstavljanje zapisa v tabelo datoteka
    if ($datoteka_id) {
        $sql_update = "UPDATE datoteka SET potDatoteke = :pot, datumNastanka = CURRENT_TIMESTAMP WHERE id = :datoteka_id";
        $stmt_update = $db->prepare($sql_update);
        $stmt_update->bindParam(':pot', $full_file_path);
        $stmt_update->bindParam(':datoteka_id', $datoteka_id, PDO::PARAM_INT);
        $stmt_update->execute();
    } 
    else {
        $sql_insert = "INSERT INTO datoteka (FKprojekt, imeDatoteke, potDatoteke, jeMain) VALUES (:projekt_id, :ime, :pot, 1)";
        $stmt_insert = $db->prepare($sql_insert);
        $stmt_insert->bindParam(':projekt_id', $projekt_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':ime', $imeDatoteke);
        $stmt_insert->bindParam(':pot', $full_file_path);
        $stmt_insert->execute();
    }
    echo json_encode(['success' => true, 'message' => 'Koda uspešno shranjena in zapisana v tabelo "datoteka".']);

} catch (\Exception $e) {
    error_log("Napaka pri shranjevanju kode: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Napaka strežnika pri shranjevanju: ' . $e->getMessage()]);
}
?>