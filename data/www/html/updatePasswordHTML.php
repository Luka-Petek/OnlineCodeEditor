<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: prijava.html");
    exit;
}

$error_message = $_SESSION['error_message'] ?? null;
$success_message = $_SESSION['success_message'] ?? null;

unset($_SESSION['error_message']);
unset($_SESSION['success_message']);

$theme = $_COOKIE['theme'] ?? 'dark';
?>

<!DOCTYPE html>
<html lang="sl" data-theme="<?php echo $theme; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLab - Sprememba gesla</title>
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
            <h2>Spremeni geslo</h2>
            <p style="margin-bottom: 20px; color: var(--text-muted, #888);">Za večjo varnost redno posodabljajte svoje geslo.</p>

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

            <form action="php/updatePassword.php" method="POST">
                <div class="form-group">
                    <label for="current_password">Trenutno geslo</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Novo geslo</label>
                    <input type="password" id="new_password" name="new_password" required minlength="8">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Potrdi novo geslo</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                </div>

                <button type="submit" class="submit-btn">POSODOBI GESLO</button>
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