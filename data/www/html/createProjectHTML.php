<?php
session_start();

$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);

$jezik = $_GET['jezik'] ?? 'neznan';

if ($jezik === 'neznan') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLab - Nov Projekt</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header class="mainHeader">
        <div>
            <h1>Nov Projekt v <span id="jezik-naslov"><?= htmlspecialchars(strtoupper($jezik)) ?></span></h1>
            <p>Vnesite podrobnosti o vašem novem projektu.</p>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2 class="form-title">Podrobnosti projekta v <?= htmlspecialchars(strtoupper($jezik)) ?></h2>

                <form action="php/createProject.php" method="POST" class="auth-form">

                    <!-- Skrito polje za prenos ID jezika ??-->
                    <input type="hidden" name="jezik" value="<?= htmlspecialchars($jezik) ?>">

                    <div class="form-group">
                        <label for="imeProjekta">Ime Projekta</label>
                        <input type="text" id="imeProjekta" name="imeProjekta" required
                            placeholder="Npr. MojaPrva<?= htmlspecialchars(strtoupper($jezik)) ?>Aplikacija"
                            maxlength="255">
                        <small>Ime mora biti edinstveno za vaš račun.</small>
                    </div>

                    <div class="form-group">
                        <label for="opis">Opis Projekta (neobvezno)</label>
                        <textarea id="opis" name="opis" rows="4"
                            placeholder="Kratek opis, kaj bo projekt počel."></textarea>
                    </div>

                    <?php if (!empty($error_message)): ?>
                        <p style="color: var(--color-danger); text-align: center; margin-bottom: 15px;">
                            <?= $error_message ?>
                        </p>
                    <?php endif; ?>

                    <button type="submit" class="submit-btn">Ustvari Projekt</button>

                </form>

                <p class="mt-4">
                    <a href="index.php" class="link-secondary">Prekliči in nazaj na izbiro jezika</a>
                </p>

            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            &copy; 2025 CodeLab. Vse pravice pridržane.
        </div>
    </footer>
</body>

</html>