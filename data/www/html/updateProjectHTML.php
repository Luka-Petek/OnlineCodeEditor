<?php
session_start();

// Preverjanje prijave
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: prijava.html");
    exit;
}

// Pridobivanje podatkov iz seje (ki jih je nastavil updateProject.php ob GET zahtevi)
$projekt = $_SESSION['edit_data'] ?? null;
$form_data = $_SESSION['form_data'] ?? null;

// Če imamo napake pri prejšnjem poskusu shranjevanja, uporabimo tiste podatke
$ime = htmlspecialchars($form_data['imeProjekta'] ?? $projekt['imeProjekta'] ?? '');
$opis = htmlspecialchars($form_data['opis'] ?? $projekt['opis'] ?? '');
$jezik = htmlspecialchars($form_data['jezik'] ?? $projekt['jezik'] ?? '');
$id = htmlspecialchars($projekt['id'] ?? $_GET['id'] ?? '');

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
unset($_SESSION['form_data']);

$theme = $_COOKIE['theme'] ?? 'dark';
?>

<!DOCTYPE html>
<html lang="sl" data-theme="<?php echo $theme; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLab - Uredi Projekt</title>
    <link rel="icon" type="image/png" href="pictures/icon.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/tema.js"></script>
    <style>
        body {
            display: grid;
            grid-template-rows: auto 1fr auto;
            min-height: 100vh;
        }

        main,
        header,
        footer {
            display: grid;
            justify-content: center;
            align-items: center;
        }

        /* Dodatek za textarea, da se ujema z inputi */
        textarea {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid var(--border-color, #333);
            background-color: var(--input-bg, #1a1a1a);
            color: var(--text-color, white);
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }
        
        select {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid var(--border-color, #333);
            background-color: var(--input-bg, #1a1a1a);
            color: var(--text-color, white);
        }
    </style>
</head>

<body>

    <header>
        <div class="staticHeader">
            <div class="logo">
                <span class="kodeLab">Code</span>Lab
            </div>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <h2>Uredi projekt</h2>
            <p style="margin-bottom: 20px; color: var(--text-muted, #888);">Spremenite podrobnosti vašega projekta.</p>

            <?php if ($error_message): ?>
                <div style="padding: 0.75rem; background-color: #ef4444; color: white; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="php/updateProject.php" method="POST">
                <input type="hidden" name="projektId" value="<?php echo $id; ?>">

                <div class="form-group">
                    <label for="imeProjekta">Ime projekta</label>
                    <input type="text" id="imeProjekta" name="imeProjekta" value="<?php echo $ime; ?>" required>
                </div>

                <div class="form-group">
                    <label for="jezik">Programski jezik</label>
                    <select id="jezik" name="jezik" required>
                        <option value="python" <?php echo ($jezik == 'python') ? 'selected' : ''; ?>>Python</option>
                        <option value="java" <?php echo ($jezik == 'java') ? 'selected' : ''; ?>>Java</option>
                        <option value="javascript" <?php echo ($jezik == 'javascript') ? 'selected' : ''; ?>>JavaScript</option>
                        <option value="c" <?php echo ($jezik == 'c') ? 'selected' : ''; ?>>C</option>
                        <option value="html" <?php echo ($jezik == 'html') ? 'selected' : ''; ?>>HTML</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="opis">Opis projekta</label>
                    <textarea id="opis" name="opis"><?php echo $opis; ?></textarea>
                </div>

                <button type="submit" class="submit-btn">Shrani spremembe</button>
            </form>

            <a href="dashboard.php" class="prijavaRegistracija">PREKLIČI IN NAZAJ</a>
        </div>
    </main>

    <footer>
        <div class="container">
            &copy; 2025 CodaLab. Vse pravice pridržane.
        </div>
    </footer>

</body>
</html>