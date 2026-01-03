<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docker Terminal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200 p-8">
    <div class="max-w-4xl mx-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-blue-400">Docker Diagnostic Tool</h1>
            <p class="text-slate-400">Uporabnik: <span class="text-green-400"><?php echo posix_getpwuid(posix_geteuid())['name']; ?></span></p>
        </header>

        <div class="grid gap-6">
            <!-- Preverjanje binarne datoteke -->
            <section class="bg-slate-800 p-6 rounded-lg border border-slate-700">
                <h2 class="text-xl font-semibold mb-4 text-slate-100 border-b border-slate-700 pb-2">1. Preverjanje binarne poti</h2>
                <?php
                $dockerPath = '/usr/bin/docker/docker';
                if (file_exists($dockerPath)) {
                    echo '<p class="text-green-500">✓ Binarna datoteka najdena na: ' . $dockerPath . '</p>';
                    echo '<p class="text-sm text-slate-400">Dovoljenja: ' . substr(sprintf('%o', fileperms($dockerPath)), -4) . '</p>';
                } else {
                    echo '<p class="text-red-500">✗ Binarna datoteka NE OBSTAJA na ' . $dockerPath . '</p>';
                }
                ?>
            </section>

            <!-- Preverjanje Socket-a -->
            <section class="bg-slate-800 p-6 rounded-lg border border-slate-700">
                <h2 class="text-xl font-semibold mb-4 text-slate-100 border-b border-slate-700 pb-2">2. Preverjanje Docker vtičnice</h2>
                <?php
                $socketPath = '/var/run/docker.sock';
                if (is_writable($socketPath)) {
                    echo '<p class="text-green-500">✓ Vtičnica je zapisljiva za www-data.</p>';
                } else {
                    echo '<p class="text-yellow-500">! Vtičnica obstaja, a morda ni zapisljiva (Preveri chmod 666).</p>';
                }
                ?>
            </section>

            <!-- Izvedba ukaza -->
            <section class="bg-slate-800 p-6 rounded-lg border border-slate-700">
                <h2 class="text-xl font-semibold mb-4 text-slate-100 border-b border-slate-700 pb-2">3. Izpis kontejnerjev (docker ps)</h2>
                <div class="bg-black p-4 rounded font-mono text-sm overflow-x-auto text-emerald-400">
                    <pre><?php
                    // Uporabimo absolutno pot do binarne datoteke
                    $output = [];
                    $resultCode = 0;
                    exec("$dockerPath ps -a 2>&1", $output, $resultCode);
                    
                    if (empty($output)) {
                        echo "Ni odziva sistema.";
                    } else {
                        echo htmlspecialchars(implode("\n", $output));
                    }
                    ?></pre>
                </div>
                <div class="mt-4 text-sm">
                    Status koda: <span class="<?php echo $resultCode === 0 ? 'text-green-400' : 'text-red-400'; ?>"><?php echo $resultCode; ?></span>
                </div>
            </section>
        </div>
    </div>
</body>
</html>

<!--
"fix": 

sudo chmod 666 /var/run/docker.sock
-->