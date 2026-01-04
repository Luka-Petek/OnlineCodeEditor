<?php
session_start();
require_once 'db.php'; 

header('Content-Type: application/json');

// 1. Preverjanje avtentikacije
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401); 
    echo json_encode(['success' => false, 'output' => 'Napaka pri avtentikaciji ali neveljavna metoda.']);
    exit;
}

// 2. Pridobivanje vhodnih podatkov
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$projekt_id = $data['projektId'] ?? null;
$koda = $data['koda'] ?? null;
$jezik = $data['jezik'] ?? null; 
$fk_uporabnik = $_SESSION['user_id'] ?? null;

if (!$projekt_id || !is_numeric($projekt_id) || $koda === null || !$jezik || !$fk_uporabnik) {
    http_response_code(400);
    echo json_encode(['success' => false, 'output' => 'Manjkajoči podatki za izvajanje.']);
    exit;
}

try {
    $jezik_lower = strtolower($jezik);

    // 3. Določitev ekstenzije in imena datoteke (Java potrebuje Main.java)
    if ($jezik_lower === 'java') {
        $imeDatoteke = "Main.java";
    } else {
        $ekstenzija = match ($jezik_lower) {
            'python'     => 'py', 
            'c'          => 'c', 
            'javascript' => 'js', 
            'html'       => 'html', 
            default      => 'txt',
        };
        $imeDatoteke = "main." . $ekstenzija; 
    }
    
    // 4. Poti na host strežniku
    $dir_osnova = '/var/www/html/user_files/'; 
    $dir_path = $dir_osnova . $fk_uporabnik . '/' . $projekt_id . '/';
    
    // Ustvarimo mapo, če ne obstaja
    if (!is_dir($dir_path)) {
        mkdir($dir_path, 0777, true);
    }
    
    $full_file_path = $dir_path . $imeDatoteke;

    // 5. Shranjevanje datoteke
    if (file_put_contents($full_file_path, $koda) === false) {
        throw new \Exception("Napaka pri pisanju datoteke na lokacijo: $full_file_path");
    }
    
    // Nastavimo dovoljenja, da Docker vsebnik vidi datoteko
    chmod($full_file_path, 0666);

    // 6. Priprava ukaza za izvedbo znotraj Dockerja
    $izvedbeni_ukaz = "";

    switch ($jezik_lower) {
        case 'python':
            $izvedbeni_ukaz = "python3 main.py";
            break;
        case 'java':
            // Poenoteno na Main (velika začetnica) za datoteko in razred
            $izvedbeni_ukaz = "javac Main.java && java Main";
            break;
        case 'c':
            $izvedbeni_ukaz = "gcc main.c -o program && ./program";
            break;
        case 'javascript':
            $izvedbeni_ukaz = "node main.js";
            break;
        case 'html':
            echo json_encode(['success' => true, 'output' => "HTML se izvaja v brskalniku (predogled)."]);
            exit;
        default:
            throw new \Exception("Jezik $jezik_lower ni podprt za izvajanje.");
    }

    // 7. Docker konfiguracija
    $container_name = "code-run-" . $projekt_id . '-' . time();
    $executor_image = "executor"; 
    
    $docker_path = "/usr/bin/docker/docker"; 
    
    $abs_dir_path = realpath($dir_path);
    if (!$abs_dir_path) {
        throw new \Exception("Ne morem dobiti absolutne poti za: " . $dir_path);
    }

    // Sestavljanje Docker ukaza
    $docker_ukaz = sprintf(
        "export DOCKER_API_VERSION=1.44 && %s run --rm -v %s:/code --network none --memory 128m --cpus 0.5 --name %s --user root --workdir /code %s /bin/bash -c %s 2>&1",
        $docker_path, 
        escapeshellarg($abs_dir_path), 
        escapeshellarg($container_name),
        escapeshellarg($executor_image),
        escapeshellarg($izvedbeni_ukaz)
    );
    
    // 8. Izvedba ukaza
    $izhod = shell_exec($docker_ukaz);

    echo json_encode(['success' => true, 'output' => $izhod ?? "Koda se je uspešno izvedla (brez izhoda)."]);

} catch (\Exception $e) {
    error_log("Napaka pri izvajanju kode: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'output' => 'Napaka strežnika: ' . $e->getMessage()]);
}
?>