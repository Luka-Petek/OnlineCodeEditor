<?php 
include 'userInfo.php'; 

$jeziki_labels = [];
$jeziki_values = [];

try {
    $user_id = $_SESSION["user_id"];
    $sql_stats = "SELECT jezik, COUNT(*) as stevilo FROM projekt WHERE FKuporabnik = ? GROUP BY jezik";
    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute([$user_id]);
    $results = $stmt_stats->fetchAll(PDO::FETCH_ASSOC);

    foreach($results as $row) {
        $jeziki_labels[] = $row['jezik'];
        $jeziki_values[] = (int)$row['stevilo'];
    }
} 
catch (Exception $e) {
}
?>