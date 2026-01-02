<?php
session_start();
require_once 'php/vrniProjekte.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: prijava.html");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: projekti.php");
    exit;
}

$projekt_id = (int) $_GET['id'];
$fk_uporabnik = $_SESSION['user_id'];
$user_name = htmlspecialchars(string: $_SESSION['ime'] ?? 'Uporabnik');

$projekt = vrniProjektPID($projekt_id);

if (!$projekt) {
    header("location: projekti.php?error=access_denied");
    exit;
}

// *** LOGIKA ZA NALAGANJE SHRANJENE KODE ***
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

<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLab - Urejevalnik Kode - <?php echo htmlspecialchars($projekt['imeProjekta'] ?? 'Neznan Projekt'); ?>
    </title>
    <link rel="icon" type="image/png" href="pictures/icon.png">
    <link rel="stylesheet" href="css/editor.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/theme/darcula.min.css">
    <script src="js/tema.js"></script>
</head>

<body>

    <header class="header">
        <div class="project-info">
            <h1 style="font-size: 1.25rem; font-weight: 700;">
                <span class="kodeLab">Code</span>Lab
            </h1>
            <span class="project-badge">
                Projekt: **<?php echo htmlspecialchars($projekt['imeProjekta'] ?? 'Neznan Projekt'); ?>**
                (<?php echo strtoupper($projekt['jezik'] ?? 'NEZNANO'); ?>)
            </span>
        </div>
        <div class="controls">
            <button id="runButton" class="btn btn-primary">
                Zaženi Kodo
            </button>
            <button id="saveButton" class="btn btn-secondary">
                Shrani
            </button>
            <span style="font-size: 0.875rem; color: var(--color-text-secondary);"><?php echo $user_name; ?></span>
            <a href="index.php" class="prijavaRegistracija">Nazaj</a>
            <a href="php/odjava.php" class="logout-btn">Odjava</a>
        </div>
    </header>

    <main class="main-content">

        <div class="editor-container">
            <textarea id="codeEditor" name="code"><?php echo $zacetna_vsebina; ?></textarea>
        </div>

        <div class="console-container">
            <h2 class="console-header">Konzola / Izhod</h2>

            <pre id="output-container">
// Tukaj bo prikazan izhod kode.
Pritisnite "Zaženi Kodo" za simulacijo izvedbe.
            </pre>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/<?php echo $mode_script; ?>"></script>
    <?php if ($jezik === 'html'): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/css/css.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/javascript/javascript.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/htmlmixed/htmlmixed.min.js"></script>
    <?php endif; ?>

    <script>
        const codeMirrorMode = '<?php echo $codemirror_mode === 'xml' ? 'htmlmixed' : $codemirror_mode; ?>';
        const outputContainer = document.getElementById('output-container');

        const editor = CodeMirror.fromTextArea(document.getElementById('codeEditor'), {
            lineNumbers: true,
            mode: codeMirrorMode,
            theme: "darcula",
            indentUnit: 4,
            tabSize: 4,
            lineWrapping: true,
        });

        // *** SHRANI BUTTON LOGIKA (AJAX) ***
        const PROJEKT_ID = <?php echo json_encode($projekt_id); ?>;
        const JEZIK = '<?php echo $jezik; ?>';
        const SAVE_URL = 'php/shraniKodo.php';
        const RUN_URL = 'php/izvediKodo.php';

        document.getElementById('runButton').addEventListener('click', () => {
            const data = editor.getValue();

            outputContainer.textContent = "Izvajam kodo...";

            fetch(RUN_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    projektId: PROJEKT_ID,
                    koda: data,
                    jezik: JEZIK
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => { throw new Error(error.output || `Napaka strežnika: ${response.status}`); });
                    }
                    return response.json();
                })
                .then(data => {
                    const header = '[Izhod iz docker sandboxa - ${JEZIK.toUpperCase()}]\n=================================\n`'
                    if (data.success) {
                        outputContainer.textContent = data.output;
                    } 
                    else {
                        outputContainer.textContent = `Napaka pri izvajanju: ${data.output}`;
                        console.error("Napaka:", data.output);
                    }
                })
                .catch(error => {
                    outputContainer.textContent = `Napaka: ${error.message}.`;
                    console.error('Fetch Error:', error);
                });
        });

        document.getElementById('saveButton').addEventListener('click', () => {
            const koda = editor.getValue();
            const outputContainer = document.getElementById('output-container');

            outputContainer.textContent = "Shranjujem kodo...";

            fetch(SAVE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    projektId: PROJEKT_ID,
                    koda: koda,
                    jezik: JEZIK
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => { throw new Error(error.message || `Napaka strežnika: ${response.status}`); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        outputContainer.textContent = `Shranjevanje uspešno! (${data.message})`;
                    } else {
                        outputContainer.textContent = `Napaka pri shranjevanju: ${data.message}`;
                        console.error("Napaka:", data.message);
                    }
                })
                .catch(error => {
                    outputContainer.textContent = `Napaka: ${error.message}.`;
                    console.error('Fetch Error:', error);
                });
        });
    </script>
</body>

</html>