<?php 
include 'php/userInfo.php'; 
?>
<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLab - Profil</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <header>
        <div class="staticHeader">
            <div class="logo">
                <span class="kodeLab">Code</span>Lab
            </div>
        </div>
    </header>

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
                        <span class="podatki-vrednost">Geslo je nastavljeno (shranjeno šifrirano)</span>
                    </div>

                    <div class="podatki-vrstica">
                        <span class="podatki-label">Uporabniški ID</span>
                        <span class="podatki-vrednost">#<?php echo htmlspecialchars($_SESSION["user_id"]); ?></span>
                    </div>
                </div>

                <div style="margin-top: 40px; display: flex; gap: 10px;">
                    <button class="prijavaRegistracija">Uredi profil</button>
                    <button class="prijavaRegistracija" style="background: transparent; border: 1px solid var(--color-border);">Spremeni geslo</button>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            &copy; 2025 CodeLab. Vse pravice pridržane. | <a href="#">Pogoji uporabe</a>
        </div>
    </footer>

    <script>
        // Inicializacija ikon
        lucide.createIcons();
    </script>
</body>

</html>