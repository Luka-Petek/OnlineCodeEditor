<?php
include 'php/allLang.php';
?>

<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLab - Profil</title>
    <link rel="icon" type="image/png" href="pictures/icon.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- za graf -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header>
        <div class="staticHeader">
            <div class="logo">
                <span class="kodeLab">Code</span>Lab
            </div>
        </div>
    </header>

    <nav class="main-nav">
        <ul>
            <li><a class="nav-link" href="index.php">Nazaj</a></li>
        </ul>
    </nav>

    <main class="container">
        <div class="profil">
            <section class="slikaInInfo">
                <div class="profilna-slika">
                    <i data-lucide="user"></i>
                </div>
                <p style="margin-top: 20px; color: var(--color-text-subtle); font-size: 14px;">
                    Prijavljeni ste kot: <br><strong><?php echo htmlspecialchars($ime); ?></strong>
                </p>
                <a href="php/odjava.php" style="color: #e63946; text-decoration: none; font-size: 13px; margin-top: 5px; font-weight: bold;">ODJAVA</a>
            </section>

            <section class="vsebina">
                <h2>Osebni podatki</h2>
                
                <div class="podatki-skupina">
                    <div class="podatki-vrstica">
                        <span class="podatki-label">Ime in priimek</span>
                        <span class="podatki-vrednost"><?php echo htmlspecialchars($ime . " " . $priimek); ?></span>
                    </div>

                    <div class="podatki-vrstica">
                        <span class="podatki-label">Email naslov</span>
                        <span class="podatki-vrednost"><?php echo htmlspecialchars($gmail); ?></span>
                    </div>

                    <div class="podatki-vrstica">
                        <span class="podatki-label">Varnost</span>
                        <span class="podatki-vrednost">Geslo je nastavljeno</span>
                    </div>

                    <div class="podatki-vrstica">
                        <span class="podatki-label">Uporabniški ID</span>
                        <span class="podatki-vrednost">#<?php echo htmlspecialchars($_SESSION["user_id"]); ?></span>
                    </div>
                </div>
            </section>

            <div style="margin-top: 40px; display: flex; gap: 10px;">
                <button class="prijavaRegistracija" onclick="redirectUserUpdate()">Uredi profil</button>
                <button class="prijavaRegistracija" style="background: transparent; border: 1px solid var(--color-border);">Spremeni geslo</button>
            </div>
        </div>

        <section class="profil-graf-container">
            <h2>Porazdelitev jezikov</h2>
            <div class="chart-wrapper">
                <?php if (!empty($jeziki_values)): ?>
                    <canvas id="jezikChart"></canvas>
                <?php else: ?>
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                        <p style="color: var(--color-text-subtle);">Ni podatkov za vizualizacijo.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            &copy; 2025 CodeLab. Vse pravice pridržane. | <a href="#">Pogoji uporabe</a>
        </div>
    </footer>

    <script src="js/tema.js"></script>
    <script>
        lucide.createIcons();

        function redirectUserUpdate(){
            window.location.href='updateProfileHTML.php';
        }

        const ctx = document.getElementById('jezikChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut', 
                data: {
                    labels: <?php echo json_encode($jeziki_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($jeziki_values); ?>,
                        backgroundColor: [
                            '#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0', '#4895ef'
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-subtle').trim() || '#666',
                                font: { size: 12 }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>