<?php

//shranjevanje kode
require_once 'php/db.php';
$db = getDatabaseConnection();

$jezik = strtolower($projekt['jezik'] ?? 'default');
$zacetna_vsebina_raw = '';

$ekstenzija = match ($jezik) {
    'java' => 'java',
    'python' => 'py',
    'c' => 'c',
    'javascript' => 'js',
    'html' => 'html',
    default => 'txt',
};
$imeDatoteke = "main." . $ekstenzija;

$sql_check_file = "SELECT potDatoteke FROM datoteka WHERE FKprojekt = :projekt_id AND imeDatoteke = :imeDatoteke";
$stmt_check_file = $db->prepare($sql_check_file);
$stmt_check_file->bindParam(':projekt_id', $projekt_id, PDO::PARAM_INT);
$stmt_check_file->bindParam(':imeDatoteke', $imeDatoteke);
$stmt_check_file->execute();
$shranjena_pot = $stmt_check_file->fetchColumn();


if ($shranjena_pot) {
    if (file_exists($shranjena_pot)) {
        $vsebina_iz_diska = file_get_contents($shranjena_pot);

        if ($vsebina_iz_diska !== false) {
            $zacetna_vsebina_raw = $vsebina_iz_diska;
        } else {
            error_log("Napaka pri branju datoteke: " . $shranjena_pot);
        }
    } else {
        error_log("Datoteka ni najdena na disku, čeprav je zapisana v bazi: " . $shranjena_pot);
    }
}

if (empty(trim($zacetna_vsebina_raw))) {
    switch ($jezik) {
        case 'java':
            $zacetna_vsebina_raw = "public class Main {\n\tpublic static void main(String[] args) {\n\t\tSystem.out.println(\"Pozdravljen, Java!\");\n\t}\n}";
            break;
        case 'python':
            $zacetna_vsebina_raw = "def main():\n\tprint(\"Pozdravljen, Python!\")\n\nif __name__ == \"__main__\":\n\tmain()";
            break;
        case 'c':
            $zacetna_vsebina_raw = "#include <stdio.h>\n\nint main() {\n\tprintf(\"Pozdravljen, C!\\n\");\n\treturn 0;\n}";
            break;
        case 'javascript':
            $zacetna_vsebina_raw = "console.log('Pozdravljen, JavaScript!');";
            break;
        case 'html':
            $project_title = htmlspecialchars($projekt['imeProjekta'] ?? 'Projekt');
            $zacetna_vsebina_raw = "<!DOCTYPE html>\n<html>\n<head>\n\t<title>" . $project_title . "</title>\n</head>\n<body>\n\t<h1>Pozdravljen, HTML!</h1>\n</body>\n</html>";
            break;
        default:
            $zacetna_vsebina_raw = "// Jezik ni prepoznan. Vnesite svojo kodo.";
    }
}
$zacetna_vsebina = htmlspecialchars($zacetna_vsebina_raw);

$codemirror_mode = 'clike';
$mode_script = 'mode/clike/clike.min.js';

switch ($jezik) {
    case 'python':
        $codemirror_mode = 'python';
        $mode_script = 'mode/python/python.min.js';
        break;
    case 'javascript':
        $codemirror_mode = 'javascript';
        $mode_script = 'mode/javascript/javascript.min.js';
        break;
    case 'html':
        $codemirror_mode = 'xml';
        $mode_script = 'mode/xml/xml.min.js';
        break;
    case 'java':
    case 'c':
        $codemirror_mode = 'text/x-java';
        $mode_script = 'mode/clike/clike.min.js';
        break;
    default:
        $mode_script = 'mode/clike/clike.min.js';
        break;
}

?>