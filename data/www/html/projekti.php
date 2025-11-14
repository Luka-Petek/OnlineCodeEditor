<?php
session_start();
require_once 'php/db.php'; 

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: prijava.html");
    exit;
}

$user_name = htmlspecialchars($_SESSION['ime'] ?? 'Uporabnik');
$fk_uporabnik = $_SESSION['user_id'];

$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';
$theme_icon = ($theme === 'dark') ? 'sun' : 'moon';

// PRIDOBIVANJE PROJEKTOV IZ BAZE
$projekti = [];
$error_message = null;

try {
    $db = getDatabaseConnection();

    $sql = "SELECT id, imeProjekta, opis, jezik, datumNastanka 
            FROM projekt 
            WHERE FKuporabnik = :fk_uporabnik 
            ORDER BY datumNastanka DESC";
            
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':fk_uporabnik', $fk_uporabnik, PDO::PARAM_INT); // Predpostavljamo, da je user_id INTEGER
    $stmt->execute();
    $projekti = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (\PDOException $e) {
    error_log("Napaka pri pridobivanju projektov: " . $e->getMessage());
    $error_message = "Prišlo je do sistemske napake pri nalaganju vaših projektov.";
} catch (\Exception $e) {
     $error_message = "Napaka: Ne morem se povezati z bazo podatkov.";
}
function getLangStyle($jezik) {
    $jezik = strtolower($jezik);
    switch ($jezik) {
        case 'java':
            return ['icon' => '☕', 'color' => '#f87171'];
        case 'python':
            return ['icon' => '🐍', 'color' => '#facc15'];
        case 'c':
            return ['icon' => '🔧', 'color' => '#60a5fa'];
        case 'javascript':
            return ['icon' => '⚡', 'color' => '#fcd34d'];
        case 'html':
            return ['icon' => '🌐', 'color' => '#fb923c'];
        default:
            return ['icon' => '📄', 'color' => '#8b949e'];
    }
}
?>

<!DOCTYPE html>
<html lang="sl" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodaLab - Moji Projekti</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <div class="staticHeader">
            <div class="logo">
                <span class="kodeLab">Code</span>Lab
            </div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <button id="themeToggle" title="Spremeni temo"
                    style="padding: 8px; border-radius: 50%; background: none; border: none; color: var(--color-text-subtle); cursor: pointer; transition: color 0.2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-<?php echo $theme_icon; ?>">
                        <?php if ($theme_icon === 'sun'): ?>
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 2v2" />
                            <path d="M12 20v2" />
                            <path d="m4.93 4.93 1.41 1.41" />
                            <path d="m17.66 17.66 1.41 1.41" />
                            <path d="m2 12h2" />
                            <path d="m20 12h2" />
                            <path d="m6.34 17.66-1.41 1.41" />
                            <path d="m19.07 4.93-1.41 1.41" />
                        <?php else: ?>
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                        <?php endif; ?>
                    </svg>
                </button>

                <span class="greeting-message">Pozdravljen, <?php echo $user_name; ?>!</span>
                <a href="php/odjava.php" class="logout-btn">Odjava</a>
            </div>
        </div>
    </header>

    <header class="mainHeader">
        <div class="container">
            <h1>Moji Projekti</h1>
            <p>Seznam vseh projektov, ki so shranjeni pod vašim računom. Kliknite za nadaljevanje urejanja.</p>
        </div>

        <nav class="main-nav">
            <ul>
                <li><a class="nav-link" href="index.php">Nazaj</a></li>
            </ul>
        </nav>

    </header>

    <main>
        <div class="container">
            <h2 class="section-title">Aktivni in shranjeni projekti</h2>

            <?php if (isset($error_message)): ?>
                <div style="padding: 1rem; background-color: #ef4444; color: white; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($projekti)): ?>
                <div class="no-projects">
                    <h2>Trenutno nimate shranjenih projektov</h2>
                    <p>Začnite z ustvarjanjem novega projekta na <a href="index.php">glavni strani</a>.</p>
                </div>
            <?php else: ?>
                <div class="cards-grid">
                    <?php foreach ($projekti as $projekt):
                        $style = getLangStyle($projekt['jezik']);
                        $jezik_lower = strtolower($projekt['jezik']);
                        $datum = new DateTime($projekt['datumNastanka']);
                    ?>
                        <a href="dashboard.php?id=<?php echo $projekt['id']; ?>" class="card" data-lang="<?php echo $jezik_lower; ?>">
                            <div>
                                <div class="card-header">
                                    <span class="card-icon" style="color: <?php echo $style['color']; ?>;"><?php echo $style['icon']; ?></span>
                                    <h3 class="card-title"><?php echo htmlspecialchars($projekt['imeProjekta']); ?></h3>
                                </div>
                                <p class="card-description">
                                    <?php echo htmlspecialchars($projekt['opis'] ?? 'Ni opisa projekta.'); ?>
                                </p>
                            </div>
                            <div class="card-footer">
                                <span class="card-language"><?php echo strtoupper($projekt['jezik']); ?></span>
                                <span>Ustvarjeno: <?php echo $datum->format('d.m.Y'); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            &copy; 2025 CodaLab. Vse pravice pridržane. | <a href="#">Pogoji uporabe</a>
        </div>
    </footer>
    
    <script>
        document.getElementById('themeToggle').addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            document.cookie = `theme=${newTheme}; path=/; max-age=31536000`;

            const iconSvg = document.querySelector('#themeToggle svg');
            if (iconSvg) {
                iconSvg.classList.remove('lucide-sun', 'lucide-moon');
                const newIconClass = `lucide-${newTheme === 'dark' ? 'sun' : 'moon'}`;
                iconSvg.classList.add(newIconClass);
                
                iconSvg.innerHTML = newTheme === 'dark' 
                    ? '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="m2 12h2"/><path d="m20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>'
                    : '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>';
            }
        });
    </script>
</body>
</html>