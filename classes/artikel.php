<?php
// auteur: Yasir
// functie: Class Artikel
namespace Bas\classes;

use Bas\classes\Database;

include_once "functions.php";

class Artikel extends Database {
    // Properties
    public $artId;
    public $artOmschrijving;
    public $artInkoop;
    public $artVerkoop;
    public $artVoorraad;
    public $artMinVoorraad;
    public $artMaxVoorraad;
    public $artLocatie;
    private $table_name = "Artikel";

    // Constructor
    public function __construct() {
        parent::__construct(); // Roep de constructor van de Database klasse aan
    }

    // CRUD Methods

    public function crudArtikel() : void {
        $lijst = $this->getArtikelen();
        $this->showTable($lijst);
    }

    public function getArtikelen() : array {
        $sql = "SELECT artId, artOmschrijving, artInkoop, artVerkoop, artVoorraad, artMinVoorraad, artMaxVoorraad, artLocatie FROM " . $this->table_name;
        $stmt = $this->getConnection()->query($sql);
        $lijst = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $lijst;
    }

    public function getArtikel(int $artId) : array {
        $sql = "SELECT artId, artOmschrijving, artInkoop, artVerkoop, artVoorraad, artMinVoorraad, artMaxVoorraad, artLocatie FROM " . $this->table_name . " WHERE artId = :artId";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(':artId', $artId, \PDO::PARAM_INT);
        $stmt->execute();
        $artikel = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $artikel ? $artikel : [];
    }

    public function dropDownArtikel($row_selected = -1) {
        $lijst = $this->getArtikelen();
        
        echo "<label for='Artikel'>Choose an artikel:</label>";
        echo "<select name='artId'>";
        foreach ($lijst as $row) {
            if ($row_selected == $row["artId"]) {
                echo "<option value='{$row["artId"]}' selected='selected'> {$row["artOmschrijving"]}</option>\n";
            } else {
                echo "<option value='{$row["artId"]}'> {$row["artOmschrijving"]}</option>\n";
            }
        }
        echo "</select>";
    }

    public function showTable(array $lijst) : void {
        echo "<table>";
        $header = array_keys($lijst[0]);
        unset($header[0]); // Verwijder artId van headers
        echo "<tr>";
        foreach ($header as $col) {
            echo "<th>" . htmlspecialchars($col) . "</th>";
        }
        echo "<th>Acties</th></tr>";
        foreach ($lijst as $row) {
            echo "<tr>";
            foreach (array_slice($row, 1) as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "<td>
                <form method='post' action='update.php?artId={$row["artId"]}'>
                    <button name='update'>Wzg</button>
                </form>
                <form method='post' action='delete.php?artId={$row["artId"]}'>
                    <button name='verwijderen'>Verwijderen</button>
                </form>
            </td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    public function deleteArtikel(int $artId) : bool {
        $sql = "DELETE FROM " . $this->table_name . " WHERE artId = :artId";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(':artId', $artId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateArtikel(array $data) : bool {
        $sql = "UPDATE " . $this->table_name . " 
                SET artOmschrijving = :artOmschrijving, 
                    artInkoop = :artInkoop,
                    artVerkoop = :artVerkoop,
                    artVoorraad = :artVoorraad,
                    artMinVoorraad = :artMinVoorraad,
                    artMaxVoorraad = :artMaxVoorraad,
                    artLocatie = :artLocatie
                WHERE artId = :artId";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(':artId', $data['artId'], \PDO::PARAM_INT);
        $stmt->bindParam(':artOmschrijving', $data['artOmschrijving'], \PDO::PARAM_STR);
        $stmt->bindParam(':artInkoop', $data['artInkoop'], \PDO::PARAM_STR);
        $stmt->bindParam(':artVerkoop', $data['artVerkoop'], \PDO::PARAM_STR);
        $stmt->bindParam(':artVoorraad', $data['artVoorraad'], \PDO::PARAM_INT);
        $stmt->bindParam(':artMinVoorraad', $data['artMinVoorraad'], \PDO::PARAM_INT);
        $stmt->bindParam(':artMaxVoorraad', $data['artMaxVoorraad'], \PDO::PARAM_INT);
        $stmt->bindParam(':artLocatie', $data['artLocatie'], \PDO::PARAM_STR);
        return $stmt->execute();
    }

    private function getNextArtId() : int {
        $sql = "SELECT MAX(artId) + 1 FROM " . $this->table_name;
        return (int) $this->getConnection()->query($sql)->fetchColumn();
    }

    public function insertArtikel(array $row) : bool {
        $artId = $this->getNextArtId();
        $sql = "INSERT INTO " . $this->table_name . " (artId, artOmschrijving, artInkoop, artVerkoop, artVoorraad, artMinVoorraad, artMaxVoorraad, artLocatie) 
                VALUES (:artId, :artOmschrijving, :artInkoop, :artVerkoop, :artVoorraad, :artMinVoorraad, :artMaxVoorraad, :artLocatie)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(':artId', $artId, \PDO::PARAM_INT);
        $stmt->bindParam(':artOmschrijving', $row['artOmschrijving'], \PDO::PARAM_STR);
        $stmt->bindParam(':artInkoop', $row['artInkoop'], \PDO::PARAM_STR);
        $stmt->bindParam(':artVerkoop', $row['artVerkoop'], \PDO::PARAM_STR);
        $stmt->bindParam(':artVoorraad', $row['artVoorraad'], \PDO::PARAM_INT);
        $stmt->bindParam(':artMinVoorraad', $row['artMinVoorraad'], \PDO::PARAM_INT);
        $stmt->bindParam(':artMaxVoorraad', $row['artMaxVoorraad'], \PDO::PARAM_INT);
        $stmt->bindParam(':artLocatie', $row['artLocatie'], \PDO::PARAM_STR);
        return $stmt->execute();
    }
}
?>