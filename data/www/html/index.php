<?php
session_start();

$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_name = $is_logged_in ? htmlspecialchars($_SESSION['ime']) : '';

$current_user_id = $is_logged_in ? ($_SESSION['user_id'] ?? 0) : 0;

$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';
$theme_icon = ($theme === 'dark') ? 'sun' : 'moon';
?>

<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodaLab - Spletni Urejevalnik Kode</title>
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
                        class="lucide lucide-sun">
                        <circle cx="12" cy="12" r="4" />
                        <path d="M12 2v2" />
                        <path d="M12 20v2" />
                        <path d="m4.93 4.93 1.41 1.41" />
                        <path d="m17.66 17.66 1.41 1.41" />
                        <path d="m2 12h2" />
                        <path d="m20 12h2" />
                        <path d="m6.34 17.66-1.41 1.41" />
                        <path d="m19.07 4.93-1.41 1.41" />
                    </svg>
                </button>

                <?php if ($is_logged_in): ?>
                    <span class="greeting-message">Pozdravljen, <?php echo $user_name; ?>!</span>
                    <a href="php/odjava.php" class="logout-btn">Odjava</a>
                    <br>

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
                <li><a class="nav-link" href="projekti.php">Projekti</a></li>
                <li><a href="#" class="nav-link">Profil</a></li>
                <li><a href="#" class="nav-link">O nas</a></li>
            </ul>
        </nav>
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
                }
                else {
                    alert('Za ustvarjanje novega projekta se morate prijaviti.');
                    window.location.href = 'prijava.html';
                }
            });
        });
    </script>
</body>

</html>