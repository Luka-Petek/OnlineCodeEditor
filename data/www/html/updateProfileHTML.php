<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: prijava.html");
    exit;
}

$user = $_SESSION['user_data'] ?? null;
$form_data = $_SESSION['form_data'] ?? null;

$ime = htmlspecialchars($form_data['ime'] ?? $user['ime'] ?? '');
$priimek = htmlspecialchars($form_data['priimek'] ?? $user['priimek'] ?? '');
$gmail = htmlspecialchars($form_data['gmail'] ?? $user['gmail'] ?? '');

$error_message = $_SESSION['error_message'] ?? null;
$success_message = $_SESSION['success_message'] ?? null;

unset($_SESSION['error_message']);
unset($_SESSION['success_message']);
unset($_SESSION['form_data']);

$theme = $_COOKIE['theme'] ?? 'dark';
?>

<!DOCTYPE html>
<html lang="sl" data-theme="<?php echo $theme; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLab - Uredi Profil</title>
    <link rel="icon" type="image/png" href="pictures/icon.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/tema.js"></script>
    <style>
        body {
            display: grid;
            grid-template-rows: auto 1fr auto;
            min-height: 100vh;
        }

        main, header, footer {
            display: grid;
            justify-content: center;
            align-items: center;
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
            <h2>Uredi profil</h2>
            <p style="margin-bottom: 20px; color: var(--text-muted, #888);">Posodobite svoje osebne podatke.</p>

            <?php if ($error_message): ?>
                <div style="padding: 0.75rem; background-color: #ef4444; color: white; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div style="padding: 0.75rem; background-color: #10b981; color: white; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <form action="php/updateProfile.php" method="POST">
                <div class="form-group">
                    <label for="ime">Ime</label>
                    <input type="text" id="ime" name="ime" value="<?php echo $ime; ?>" required>
                </div>

                <div class="form-group">
                    <label for="priimek">Priimek</label>
                    <input type="text" id="priimek" name="priimek" value="<?php echo $priimek; ?>" required>
                </div>

                <div class="form-group">
                    <label for="gmail">E-poštni naslov</label>
                    <input type="gmail" id="gmail" name="gmail" value="<?php echo $gmail; ?>" required>
                </div>

                <button type="submit" class="submit-btn">SHRANI SPREMEMBE</button>
            </form>

            <a href="profil.php" class="prijavaRegistracija">NAZAJ</a>
        </div>
    </main>

    <footer>
        <div class="container">
            &copy; 2025 CodeLab. Vse pravice pridržane.
        </div>
    </footer>

</body>
</html>