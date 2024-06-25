<?php
// Auteur: Yasir
// Functie: definitie class VerkoopOrder
namespace Bas\classes;

use PDO;
use PDOException;

class VerkoopOrder extends Database {
    public $verkOrdId;
    public $klantId;
    public $artId;
    public $verkOrdDatum;
    public $verkOrdBestAantal;
    public $verkOrdStatus;
    private $table_name = "VerkoopOrder";   

    /**
     * Haal alle verkooporders op en geef ze weer in een tabel
     */
    public function crudVerkooporder(): void {
        $lijst = $this->getVerkoopOrders();
        $this->showTable($lijst);
    }

    /**
     * Haal alle verkooporders op uit de database
     * @return array
     */
    public function getVerkoopOrders(): array {
        try {
            $sql = "SELECT vo.*, k.klantNaam, a.artOmschrijving 
                    FROM $this->table_name vo
                    JOIN klant k ON vo.klantId = k.klantId
                    JOIN artikel a ON vo.artId = a.artId";
            $stmt = self::$conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Haal een specifieke verkooporder op basis van verkOrdId
     * @param int $verkOrdId
     * @return array
     */
    public function getVerkoopOrder(int $verkOrdId): array {
        try {
            $sql = "SELECT vo.*, k.klantNaam, a.artOmschrijving
                    FROM $this->table_name vo
                    JOIN klant k ON vo.klantId = k.klantId
                    JOIN artikel a ON vo.artId = a.artId
                    WHERE vo.verkOrdId = :verkOrdId";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindParam(':verkOrdId', $verkOrdId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
    
    /**
     * Genereer een dropdown-menu voor verkooporders
     * @param int $row_selected - Optioneel, de geselecteerde rij
     */
    public function dropDownVerkoopOrder($row_selected = -1): void {
        $lijst = $this->getVerkoopOrders();
        
        echo "<label for='VerkoopOrder'>Kies een verkooporder:</label>";
        echo "<select name='verkOrdId'>";
        foreach ($lijst as $row) {
            $selected = ($row_selected == $row["verkOrdId"]) ? "selected='selected'" : "";
            echo "<option value='{$row["verkOrdId"]}' $selected>Order {$row["verkOrdId"]}</option>\n";
        }
        echo "</select>";
    }

    /**
     * Toon een HTML-tabel van verkooporders
     * @param array $lijst - Array met verkoopordergegevens
     */
    public function showTable(array $lijst): void {
        echo "<table>";
        echo "<tr>";
        echo "<th>Klant Naam</th>";
        echo "<th>Artikel Omschrijving</th>";
        echo "<th>Verkooporder Datum</th>";
        echo "<th>Bestel Aantal</th>";
        echo "<th>Status</th>";
        echo "<th>Wijzig</th>";
        echo "<th>Verwijder</th>";
        echo "</tr>";

        foreach ($lijst as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["klantNaam"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["artOmschrijving"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["verkOrdDatum"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["verkOrdBestAantal"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["verkOrdStatus"]) . "</td>";
            echo "<td>
                    <form method='post' action='update.php'>
                        <input type='hidden' name='verkOrdId' value='{$row["verkOrdId"]}'>
                        <button type='submit' name='update'>Wijzig</button>
                    </form>
                </td>";
            echo "<td>
                    <form method='post' action='delete.php'>
                        <input type='hidden' name='verkOrdId' value='{$row["verkOrdId"]}'>
                        <button type='submit' name='verwijderen'>Verwijderen</button>
                    </form>
                </td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    /**
     * Verwijder een verkooporder uit de database
     * @param int $verkOrdId - Het ID van de verkooporder die moet worden verwijderd
     * @return bool - True als verwijdering succesvol, anders False
     */
    public function deleteVerkoopOrder(int $verkOrdId): bool {
        try {
            $sql = "DELETE FROM $this->table_name WHERE verkOrdId = :verkOrdId";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindParam(':verkOrdId', $verkOrdId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Update een bestaande verkooporder in de database
     * @param array $row - Associatieve array met de gegevens van de verkooporder
     * @return bool - True als update succesvol, anders False
     */
    public function updateVerkoopOrder(array $row): bool {
        try {
            $sql = "UPDATE $this->table_name 
                    SET klantId = :klantId, artId = :artId, verkOrdDatum = :verkOrdDatum, 
                        verkOrdBestAantal = :verkOrdBestAantal, verkOrdStatus = :verkOrdStatus 
                    WHERE verkOrdId = :verkOrdId";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindParam(':verkOrdId', $row['verkOrdId'], PDO::PARAM_INT);
            $stmt->bindParam(':klantId', $row['klantId'], PDO::PARAM_INT);
            $stmt->bindParam(':artId', $row['artId'], PDO::PARAM_INT);
            $stmt->bindParam(':verkOrdDatum', $row['verkOrdDatum'], PDO::PARAM_STR);
            $stmt->bindParam(':verkOrdBestAantal', $row['verkOrdBestAantal'], PDO::PARAM_INT);
            $stmt->bindParam(':verkOrdStatus', $row['verkOrdStatus'], PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Voeg een nieuwe verkooporder toe aan de database
     * @param array $verkoopordergegevens - Associatieve array met gegevens van de nieuwe verkooporder
     * @return bool - True als invoegen succesvol, anders False
     */
    public function insertVerkoopOrder(array $verkoopordergegevens): bool {
        try {
            $sql = "INSERT INTO $this->table_name (klantId, artId, verkOrdDatum, verkOrdBestAantal, verkOrdStatus) 
                    VALUES (:klantId, :artId, :verkOrdDatum, :verkOrdBestAantal, :verkOrdStatus)";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindParam(':klantId', $verkoopordergegevens['klantId'], PDO::PARAM_INT);
            $stmt->bindParam(':artId', $verkoopordergegevens['artId'], PDO::PARAM_INT);
            $stmt->bindParam(':verkOrdDatum', $verkoopordergegevens['verkOrdDatum'], PDO::PARAM_STR);
            $stmt->bindParam(':verkOrdBestAantal', $verkoopordergegevens['verkOrdBestAantal'], PDO::PARAM_INT);
            $stmt->bindParam(':verkOrdStatus', $verkoopordergegevens['verkOrdStatus'], PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Bepaal het volgende verkOrdId voor nieuwe verkooporder
     * @return int - Het volgende verkOrdId
     */
    private function BepMaxVerkOrdId(): int {
        $sql = "SELECT MAX(verkOrdId) + 1 AS next_id FROM $this->table_name";
        return (int) self::$conn->query($sql)->fetchColumn();
    }
}
?>