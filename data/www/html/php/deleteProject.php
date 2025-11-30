<?php
require_once 'db.php'; 
$db = getDatabaseConnection();

$projektId = $_POST['projekt_id'] ?? null;

if (!$projektId || !is_numeric($projektId)) {
    header("Location: /dashboard.php?error=Neveljaven ID projekta ali manjkajoč podatek.");
    exit();
}

try {

    $sql = "DELETE FROM projekt WHERE id = ?"; 

    $stmt = $db->prepare($sql);
 
    $stmt->execute([$projektId]);

    if ($stmt->rowCount() > 0) {
        header("Location: /dashboard.php?success=Projekt uspešno izbrisan.");
        exit();
    } else {
        header("Location: /dashboard.php?warning=Projekt ni bil najden ali je že izbrisan.");
        exit();
    }

} catch (\PDOException $e) {
    error_log("Napaka pri brisanju projekta: " . $e->getMessage());
    header("Location: /dashboard.php?error=Napaka pri povezavi z bazo. Poskusite znova.");
    exit();
} catch (\Exception $e) {
    error_log("Splošna napaka: " . $e->getMessage());
    header("Location: /dashboard.php?error=Prišlo je do nepričakovane napake.");
    exit();
}

?>