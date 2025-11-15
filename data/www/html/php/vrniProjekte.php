<?php
require_once 'db.php';

function vrniProjektPID($projekt_id) {
    try {
        $db = getDatabaseConnection();
        
        $sql = "SELECT id, imeProjekta, opis, jezik, FKuporabnik
                FROM projekt
                WHERE id = :projekt_id";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':projekt_id', $projekt_id, PDO::PARAM_INT);
        $stmt->execute();
        $projekt = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($projekt) {
            $projekt['vsebina'] = null;
            return $projekt;
        }

        return null;

    } catch (\Exception $e) {
        error_log("Napaka pri branju projekta po ID-ju: " . $e->getMessage());
        return null;
    }
}

function vrniProjekteUID($fk_uporabnik){
    $projekti = [];
    $error_message = null;

    try {
        $db = getDatabaseConnection();

        $sql = "SELECT id, imeProjekta, opis, jezik, datumNastanka, FKuporabnik 
            FROM projekt 
            WHERE FKuporabnik = :fk_uporabnik 
            ORDER BY datumNastanka DESC";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':fk_uporabnik', $fk_uporabnik, PDO::PARAM_INT); // Predpostavljamo, da je user_id INTEGER
        $stmt->execute();
        $projekti = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $projekti;

    } catch (\PDOException $e) {
        error_log("Napaka pri pridobivanju projektov: " . $e->getMessage());
        $error_message = "Prišlo je do sistemske napake pri nalaganju vaših projektov.";
    } catch (\Exception $e) {
        $error_message = "Napaka: Ne morem se povezati z bazo podatkov.";
    }
}
?>