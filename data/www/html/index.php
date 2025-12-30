<?php
session_start();

$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_name = $is_logged_in ? htmlspecialchars($_SESSION['ime']) : '';
$current_user_id = $is_logged_in ? ($_SESSION['user_id'] ?? 0) : 0;
?>

<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodaLab - Spletni Urejevalnik Kode</title>
    <link rel="icon" type="image/png" href="pictures/icon.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/tema.js"></script>
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
                    <svg id="themeIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    </svg>
                </button>

                <?php if ($is_logged_in): ?>
                    <span class="greeting-message">Pozdravljen, <?php echo $user_name; ?>!</span>
                    <a href="php/odjava.php" class="logout-btn">Odjava</a>
                <?php else: ?>
                    <a href="prijava.html" class="prijavaRegistracija">Prijava</a>
                    <a href="registracija.html" class="prijavaRegistracija">Registracija</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <header class="mainHeader">
        <div>
            <h1>Dobrodošli v <span class="kodeLab">CodeLab-u</span></h1>
            <p>Vaš spletni laboratorij za hitro urejanje, shranjevanje in izvajanje kode v varnem izoliranem okolju.</p>
        </div>

        <?php if ($is_logged_in): ?>
        <nav class="main-nav">
            <ul>
                <li><a href="projekti.php" class="nav-link">Projekti</a></li>
                <li><a href="profil.php" class="nav-link">Profil</a></li>
                <li><a href="oNas.html" class="nav-link">O nas</a></li>
            </ul>
        </nav>
        <?php else: ?>

        <div class="main-nav">
            <ul>
                <li><a href="oNas.html" class="nav-link">O nas</a></li>
            </ul>
        </div>

        <?php endif; ?>
    </header>

    <main>
        <div class="container">
            <h2 class="section-title">Začnite z novim projektom</h2>

            <div class="cards-grid">
                <div class="card" data-lang="java">
                    <div class="card-header">
                        <span class="card-icon" style="color: #f87171;">☕</span>
                        <h3 class="card-title">Java</h3>
                    </div>
                    <p class="card-description">Robusno in kompleksno programiranje.</p>
                </div>

                <div class="card" data-lang="c">
                    <div class="card-header">
                        <span class="card-icon" style="color: #60a5fa;">🔧</span>
                        <h3 class="card-title">C</h3>
                    </div>
                    <p class="card-description">Visoka zmogljivost in sistemski razvoj.</p>
                </div>

                <div class="card" data-lang="python">
                    <div class="card-header">
                        <span class="card-icon" style="color: #facc15;">🐍</span>
                        <h3 class="card-title">Python</h3>
                    </div>
                    <p class="card-description">AI, podatki in skriptiranje.</p>
                </div>

                <div class="card" data-lang="javascript">
                    <div class="card-header">
                        <span class="card-icon" style="color: #fcd34d;">⚡</span>
                        <h3 class="card-title">JavaScript</h3>
                    </div>
                    <p class="card-description">Dinamične spletne aplikacije.</p>
                </div>

                <div class="card" data-lang="html">
                    <div class="card-header">
                        <span class="card-icon" style="color: #fb923c;">🌐</span>
                        <h3 class="card-title">HTML/CSS</h3>
                    </div>
                    <p class="card-description">Frontend struktura in stil.</p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            &copy; 2025 CodaLab. Vse pravice pridržane. | <a href="#">Pogoji uporabe</a>
        </div>
    </footer>

<script>
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', () => {
            const lang = card.dataset.lang;
            const isLoggedIn = <?= $is_logged_in ? 'true' : 'false' ?>;
            if (isLoggedIn) {
                window.location.href = 'createProjectHTML.php?jezik=' + lang;
            } else {
                window.location.href = 'prijava.html';
            }
        });
    });
</script>
</body>
</html>