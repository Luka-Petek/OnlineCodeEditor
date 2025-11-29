<?php
session_start();
require_once 'db.php'; 

header('Content-Type: application/json');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401); 
    echo json_encode(['success' => false, 'output' => 'Napaka pri avtentikaciji ali neveljavna metoda.']);
    exit;
}

// input je recimo $input: {"projektId": 123, "koda": "print('Hello')", "jezik": "python"}
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$projekt_id = $data['projektId'] ?? null;
$koda = $data['koda'] ?? null;
$jezik = $data['jezik'] ?? null; 
$fk_uporabnik = $_SESSION['user_id'] ?? null;

// Preverjanje manjkajočih podatkov ali neveljavnega uporabnika
if (!$projekt_id || !is_numeric($projekt_id) || $koda === null || !$jezik || !$fk_uporabnik) {
    http_response_code(400);
    echo json_encode(['success' => false, 'output' => 'Manjkajoči podatki za izvajanje ali neveljaven uporabnik.']);
    exit;
}

try {
    // Klic za povezavo z bazo
    $db = getDatabaseConnection();
    
    $jezik_lower = strtolower($jezik);

    // Določitev ustrezne ekstenzije datoteke
    $ekstenzija = match ($jezik_lower) {
        'java' => 'java', 
        'python' => 'py', 
        'c' => 'c', 
        'javascript' => 'js', 
        'html' => 'html', 
        default => 'txt',
    };
    $imeDatoteke = "main." . $ekstenzija; 
    
    // Pot do datotek na PHP strežniku (host/executor kontejnerju)
    $dir_osnova = '/var/www/html/user_files/'; 
    $dir_path = $dir_osnova . $fk_uporabnik . '/' . $projekt_id . '/';
    $full_file_path = $dir_path . $imeDatoteke;

    // Pot do datoteke ZNOTRAJ Docker sandbox kontejnerja (relativna, ker je workdir /code)
    $ciljna_datoteka_notranja = 'main.' . $ekstenzija; 

    // Ustvarjanje mape rekurzivno, če ne obstaja
    if (!is_dir($dir_path)) {
        if (!mkdir($dir_path, 0755, true) && !is_dir($dir_path)) {
             throw new \RuntimeException("Mape ni bilo mogoče ustvariti.");
        }
    }
    
    if (file_put_contents($full_file_path, $koda) === false) {
        throw new \Exception("Napaka pri pisanju datoteke na disk pred izvajanjem.");
    }

    $izvedbeni_ukaz = "";
    // Escajpanje mape, ki jo mapiramo v Docker, za preprečitev shell injection
    $temp_dir = escapeshellarg($dir_path);

    // Priprava izvedbenega ukaza, ki se bo zagnal ZNOTRAJ sandbox kontejnerja
    switch ($jezik_lower) {
        case 'python':
            $izvedbeni_ukaz = "python3 " . $ciljna_datoteka_notranja;
            break;
        case 'java':
            $razred = pathinfo($imeDatoteke, PATHINFO_FILENAME); 
            $komp_ukaz = "javac " . $ciljna_datoteka_notranja;
            // java -cp /code zagotovi, da najde skompilirani razred 'main'
            $izvedbeni_ukaz = $komp_ukaz . " && java -cp /code " . $razred;
            break;
        case 'c':
            $binarna_datoteka = "./a.out"; 
            $komp_ukaz = "gcc " . $ciljna_datoteka_notranja . " -o " . $binarna_datoteka;
            $izvedbeni_ukaz = $komp_ukaz . " && " . $binarna_datoteka;
            break;
        case 'javascript':
            $izvedbeni_ukaz = "node " . $ciljna_datoteka_notranja;
            break;
        case 'html':
            echo json_encode(['success' => true, 'output' => "[OPOMBA] HTML kode ni mogoče izvajati na strežniku. Za prikaz uporabite brskalnik ali vdelan iframe."]);
            exit;
        default:
            throw new \Exception("Jezik ni podprt za izvajanje.");
    }

    $container_name = "code-run-" . $projekt_id . '-' . time();
    $executor_image = "code-executor"; 

    $docker_path = "/usr/bin/docker"; 

    // Glavni ukaz za zagon Dockerja z omejenimi viri in izolacijo
    $docker_ukaz = sprintf(
        "%s run --rm -v %s:/code --network none --memory 128m --cpus 0.5 --name %s --user sandbox --workdir /code %s /bin/bash -c %s 2>&1",
        $docker_path, 
        $temp_dir, 
        escapeshellarg($container_name),
        escapeshellarg($executor_image),
        // CELOTEN UKAZ ZNOTRAJ KONTEJNERJA MORA BITI ESCAPAN
        escapeshellarg($izvedbeni_ukaz)
    );
    
    // Dejansko izvajanje Docker ukaza
    $izhod = shell_exec($docker_ukaz);

    // Vrnitev rezultata
    echo json_encode(['success' => true, 'output' => $izhod]);

} catch (\Exception $e) {
    // Napake zajemamo in filtriramo lokalne poti, preden jih prikažemo uporabniku
    $dir_osnova = '/var/www/html/user_files/';
    $clean_message = str_replace($dir_osnova, '...', $e->getMessage());
    error_log("Napaka pri izvajanje kode: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'output' => 'Napaka strežnika pri izvedbi: ' . $clean_message]);
}
?>