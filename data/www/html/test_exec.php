<?php
session_start();

function executeDockerAction($imageName, $command = "") {
    $fullCommand = sprintf("docker run --rm %s %s 2>&1", escapeshellarg($imageName), $command);
    
    $output = [];
    $returnCode = 0;
    
    exec($fullCommand, $output, $returnCode);
    
    return [
        'output' => implode("\n", $output),
        'status' => ($returnCode === 0 ? 'uspeh' : 'napaka'),
        'command' => $fullCommand
    ];
}

$result = null;

if (isset($_POST['run_executor'])) {
    $result = executeDockerAction('executor');
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docker PHP Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .terminal {
            background-color: #1a1a1a;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Nadzorna plošča Docker Executorja</h1>
        
        <div class="mb-8 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700">
            <p class="font-bold">Informacija o okolju:</p>
            <p class="text-sm">Uporabnik: <strong><?php echo posix_getpwuid(posix_geteuid())['name']; ?></strong></p>
            <p class="text-sm">Docker slika: <strong>executor</strong></p>
        </div>

        <form method="POST" class="mb-6">
            <button type="submit" name="run_executor" 
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                Zaženi 'executor' vsebnik
            </button>
        </form>

        <?php if ($result): ?>
            <div class="mt-6">
                <h2 class="text-lg font-semibold mb-2">Rezultat izvajanja:</h2>
                <div class="p-2 bg-gray-200 rounded-t-lg text-xs font-mono text-gray-600">
                    Izveden ukaz: <?php echo htmlspecialchars($result['command']); ?>
                </div>
                <pre class="terminal p-4 rounded-b-lg overflow-x-auto min-h-[200px]"><?php 
                    echo htmlspecialchars($result['output']); 
                ?></pre>
                
                <div class="mt-4">
                    <span class="px-3 py-1 rounded-full text-sm font-bold <?php echo $result['status'] === 'uspeh' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                        Status: <?php echo strtoupper($result['status']); ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <footer class="mt-12 pt-6 border-t border-gray-200 flex justify-between text-sm text-gray-500">
            <a href="diagnostika.php" class="text-blue-500 hover:underline">Odpri diagnostiko sistema</a>
            <span>Docker-PHP v1.0</span>
        </footer>
    </div>
</body>
</html>