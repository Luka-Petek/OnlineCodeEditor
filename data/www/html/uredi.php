<?php
session_start();

// Preveri, ali so podatki za urejanje prisotni v seji
$projekt_data = $_SESSION['edit_data'] ?? null;
$error_message = $_SESSION['error_message'] ?? '';

// Po prenosu prečistimo sporočila o napakah iz seje, da se ne pojavijo ob osvežitvi
unset($_SESSION['error_message']);

// Če podatkov ni, je potrebno preusmerjanje (simulacija)
if (!$projekt_data) {
    $_SESSION['error_message'] = "Manjkajo podatki za urejanje projekta. Prosimo, izberite projekt na armaturni plošči.";
    header('Location: dashboard.php'); // Predvidevamo, da je to dashboard
    exit;
}

// Inicializacija vrednosti iz seje za predhodno izpolnitev forme
$projekt_id = htmlspecialchars($projekt_data['id'] ?? '');
$imeProjekta = htmlspecialchars($projekt_data['imeProjekta'] ?? '');
$opis = htmlspecialchars($projekt_data['opis'] ?? '');
$jezik = htmlspecialchars($projekt_data['jezik'] ?? '');

$jeziki = [
    'html' => 'HTML & CSS',
    'javascript' => 'JavaScript',
    'react' => 'React/JSX',
    'angular' => 'Angular/TypeScript',
    'python' => 'Python',
    'c' => 'C/C++',
    'latex' => 'LaTeX'
];

?>
<!DOCTYPE html>

<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uredi Projekt - <?php echo $imeProjekta; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Dodajanje Font Awesome za ikone (kot na vašem primeru dashboard.php) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Ohranimo enake osnovne stile za centriranje kot v vašem primeru */
        body {
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        main,
        header,
        footer {
            display: grid;
            justify-content: center;
            align-items: center;
        }
        
        /* Stili za sporočilo o napaki */
        <?php if ($error_message): ?>
        .error-message {
            background-color: #fca5a5;
            color: #7f1d1d;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dc2626;
            font-weight: 500;
        }
        <?php endif; ?>
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical; /* Omogoči vertikalno spreminjanje velikosti */
        }
        
        /* Za drop-down select */
        .form-group select {
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236d6d6d%22%20d%3D%22M287%20172.9L154.9%203.4c-5.4-5.4-14.2-5.4-19.6%200L5.4%20172.9c-5.4%205.4-5.4%2014.2%200%2019.6s14.2%205.4%2019.6%200L145.2%2042.3c3.7-3.7%209.8-3.7%2013.5%200l119.8%20140.2c5.4%205.4%2014.2%205.4%2019.6%200s5.4-14.2%200-19.6z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 10px top 50%;
            background-size: 10px auto;
            padding-right: 30px;
        }

    </style>

    <header>
        <div class="staticHeader">
            <div class="logo">
                <span class="kodeLab">Code</span>Lab
            </div>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <h2>Urejanje Projekta</h2>
            
            <!-- Prikaz ID-ja projekta in sporočil o napakah (če obstajajo) -->
            <p style="text-align: center; margin-top: -15px; margin-bottom: 25px; font-size: 14px; color: var(--color-text-subtle, #aaa);">
                Urejanje projekta ID: <strong><?php echo $projekt_id; ?></strong>
            </p>

            <?php if ($error_message): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>
            
            <!-- FORMULAR ZA UREJANJE -->
            <form action="uredi.php" method="POST">

                <!-- Skrito polje za ID projekta (ključno za UPDATE) -->
                <input type="hidden" name="projektId" value="<?php echo $projekt_id; ?>">

                <div class="form-group">
                    <label for="imeProjekta">Ime Projekta</label>
                    <input type="text" id="imeProjekta" name="imeProjekta" 
                           value="<?php echo $imeProjekta; ?>" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="opis">Opis (Kratek povzetek)</label>
                    <!-- Uporabimo textarea namesto input za daljši opis -->
                    <textarea id="opis" name="opis" rows="4" class="form-control"><?php echo $opis; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="jezik">Jezik projekta</label>
                    <select id="jezik" name="jezik" required>
                        <?php foreach ($jeziki as $key => $value): ?>
                            <option value="<?php echo $key; ?>" 
                                    <?php echo ($jezik === $key) ? 'selected' : ''; ?>>
                                <?php echo $value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Posodobi Projekt
                </button>
            </form>

            <!-- Povezava nazaj -->
            <a href="dashboard.php" class="prijavaRegistracija" style="margin-top: 20px;">
                <i class="fas fa-arrow-left"></i> NAZAJ NA ARMATURNO PLOŠČO
            </a>

            <div class="login-link">
                <i class="fas fa-info-circle"></i> Samo posodobljena polja se bodo spremenila.
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            &copy; 2025 CodaLab. Vse pravice pridržane.
        </div>
    </footer>

</body>

</html>