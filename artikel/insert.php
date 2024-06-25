<?php
// auteur: Yasir
// functie: Class Artikel Insert
require '../../vendor/autoload.php';
use Bas\classes\Database;

class Artikel extends Database {
    private $table_name = "Artikel";
    private $order_table_name = "VerkoopOrder";
    private $klant_table_name = "Klant";

    public function __construct() {
        parent::__construct();
    }

    public function insertArtikelWithOrder($artikelgegevens, $verkoopordergegevens) {
        try {
            self::$conn->beginTransaction();

            // Voeg artikelgegevens toe
            $sql = "INSERT INTO " . $this->table_name . " 
                    (artOmschrijving, artInkoop, artVerkoop, artVoorraad, artMinVoorraad, artMaxVoorraad, artLocatie) 
                    VALUES (:artOmschrijving, :artInkoop, :artVerkoop, :artVoorraad, :artMinVoorraad, :artMaxVoorraad, :artLocatie)";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindParam(':artOmschrijving', $artikelgegevens['artOmschrijving'], PDO::PARAM_STR);
            $stmt->bindParam(':artInkoop', $artikelgegevens['artInkoop'], PDO::PARAM_STR);
            $stmt->bindParam(':artVerkoop', $artikelgegevens['artVerkoop'], PDO::PARAM_STR);
            $stmt->bindParam(':artVoorraad', $artikelgegevens['artVoorraad'], PDO::PARAM_INT);
            $stmt->bindParam(':artMinVoorraad', $artikelgegevens['artMinVoorraad'], PDO::PARAM_INT);
            $stmt->bindParam(':artMaxVoorraad', $artikelgegevens['artMaxVoorraad'], PDO::PARAM_INT);
            $stmt->bindParam(':artLocatie', $artikelgegevens['artLocatie'], PDO::PARAM_STR);
            $stmt->execute();

            $artId = self::$conn->lastInsertId();

            // Voeg verkoopordergegevens toe
            $sql = "INSERT INTO " . $this->order_table_name . " 
                    (klantId, artId, verkOrdDatum, verkOrdBestAantal, verkOrdStatus) 
                    VALUES (:klantId, :artId, :verkOrdDatum, :verkOrdBestAantal, :verkOrdStatus)";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindParam(':klantId', $verkoopordergegevens['klantId'], PDO::PARAM_INT);
            $stmt->bindParam(':artId', $artId, PDO::PARAM_INT);
            $stmt->bindParam(':verkOrdDatum', $verkoopordergegevens['verkOrdDatum'], PDO::PARAM_STR);
            $stmt->bindParam(':verkOrdBestAantal', $verkoopordergegevens['verkOrdBestAantal'], PDO::PARAM_INT);
            $stmt->bindParam(':verkOrdStatus', $verkoopordergegevens['verkOrdStatus'], PDO::PARAM_STR);
            $stmt->execute();

            self::$conn->commit();
            return true;
        } catch (PDOException $e) {
            self::$conn->rollBack();
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function getAllKlanten() {
        try {
            $sql = "SELECT klantId, klantNaam FROM " . $this->klant_table_name;
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}

// Verwerkingslogica voor het formulier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errorMsg = "";
    if (isset($_POST['insert']) && $_POST['insert'] == "Toevoegen") {
        if (
            !empty($_POST['artOmschrijving']) && 
            !empty($_POST['artInkoop']) && 
            !empty($_POST['artVerkoop']) && 
            !empty($_POST['artVoorraad']) && 
            !empty($_POST['artMinVoorraad']) && 
            !empty($_POST['artMaxVoorraad']) && 
            !empty($_POST['artLocatie']) &&
            !empty($_POST['klantNaam']) && // Veranderd naar klantNaam
            !empty($_POST['verkOrdDatum']) && 
            !empty($_POST['verkOrdBestAantal']) && 
            !empty($_POST['verkOrdStatus'])
        ) {
            $artikelgegevens = [
                'artOmschrijving' => htmlspecialchars($_POST['artOmschrijving']),
                'artInkoop' => htmlspecialchars($_POST['artInkoop']),
                'artVerkoop' => htmlspecialchars($_POST['artVerkoop']),
                'artVoorraad' => htmlspecialchars($_POST['artVoorraad']),
                'artMinVoorraad' => htmlspecialchars($_POST['artMinVoorraad']),
                'artMaxVoorraad' => htmlspecialchars($_POST['artMaxVoorraad']),
                'artLocatie' => htmlspecialchars($_POST['artLocatie'])
            ];

            $verkoopordergegevens = [
                'klantNaam' => htmlspecialchars($_POST['klantNaam']), // Veranderd naar klantNaam
                'verkOrdDatum' => htmlspecialchars($_POST['verkOrdDatum']),
                'verkOrdBestAantal' => htmlspecialchars($_POST['verkOrdBestAantal']),
                'verkOrdStatus' => htmlspecialchars($_POST['verkOrdStatus'])
            ];

            $artikel = new Artikel();

            // Haal klant ID op basis van klantnaam
            $klanten = $artikel->getAllKlanten();
            $klantId = null;
            foreach ($klanten as $klant) {
                if ($klant['klantNaam'] === $verkoopordergegevens['klantNaam']) {
                    $klantId = $klant['klantId'];
                    break;
                }
            }

            if ($klantId !== null) {
                $verkoopordergegevens['klantId'] = $klantId;

                if ($artikel->insertArtikelWithOrder($artikelgegevens, $verkoopordergegevens)) {
                    $successMsg = "Artikel en verkooporder succesvol toegevoegd!";
                } else {
                    $errorMsg = "Er is een fout opgetreden bij het toevoegen van het artikel en de verkooporder.";
                }
            } else {
                $errorMsg = "Klantnaam niet gevonden. Controleer de klantnaam en probeer opnieuw.";
            }
        } else {
            $errorMsg = "Vul alstublieft alle vereiste velden in.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toevoegen Artikel en VerkoopOrder</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <h1>CRUD Artikel en VerkoopOrder</h1>
    <h2>Toevoegen</h2>

    <?php
    if (isset($errorMsg) && $errorMsg != "") {
        echo "<p style='color: red;'>$errorMsg</p>";
    } elseif (isset($successMsg) && $successMsg != "") {
        echo "<p style='color: green;'>$successMsg</p>";
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h3>Artikel Gegevens</h3>
        <label for="oms">Artikelomschrijving:</label>
        <input type="text" id="oms" name="artOmschrijving" placeholder="Artikelomschrijving" value="<?php echo isset($_POST['artOmschrijving']) ? htmlspecialchars($_POST['artOmschrijving']) : ''; ?>" required/>
        <br>   
        <label for="ink">Inkoopprijs:</label>
        <input type="text" id="ink" name="artInkoop" placeholder="Inkoopprijs" value="<?php echo isset($_POST['artInkoop']) ? htmlspecialchars($_POST['artInkoop']) : ''; ?>" required/>
        <br>
        <label for="ver">Verkoopprijs:</label>
        <input type="text" id="ver" name="artVerkoop" placeholder="Verkoopprijs" value="<?php echo isset($_POST['artVerkoop']) ? htmlspecialchars($_POST['artVerkoop']) : ''; ?>" required/>
        <br>
        <label for="voo">Voorraad:</label>
        <input type="text" id="voo" name="artVoorraad" placeholder="Voorraad" value="<?php echo isset($_POST['artVoorraad']) ? htmlspecialchars($_POST['artVoorraad']) : ''; ?>" required/>
        <br>
        <label for="min">Minimum voorraad:</label>
        <input type="text" id="min" name="artMinVoorraad" placeholder="Minimum voorraad" value="<?php echo isset($_POST['artMinVoorraad']) ? htmlspecialchars($_POST['artMinVoorraad']) : ''; ?>" required/>
        <br>
        <label for="max">Maximum voorraad:</label>
        <input type="text" id="max" name="artMaxVoorraad" placeholder="Maximum voorraad" value="<?php echo isset($_POST['artMaxVoorraad']) ? htmlspecialchars($_POST['artMaxVoorraad']) : ''; ?>" required/>
        <br>
        <label for="loc">Locatie:</label>
        <input type="text" id="loc" name="artLocatie" placeholder="Locatie" value="<?php echo isset($_POST['artLocatie']) ? htmlspecialchars($_POST['artLocatie']) : ''; ?>" required/>
        <br>

        <h3>Verkooporder Gegevens</h3>
        <label for="klantNaam">Klant Naam:</label>
        <select id="klantNaam" name="klantNaam" required>
            <option value="">Selecteer een klant</option>
            <?php
            $artikel = new Artikel();
            $klanten = $artikel->getAllKlanten();
            foreach ($klanten as $klant) {
                echo "<option value='" . htmlspecialchars($klant['klantNaam']) . "'";
                if (isset($_POST['klantNaam']) && $_POST['klantNaam'] === $klant['klantNaam']) {
                    echo " selected";
                }
                echo ">" . htmlspecialchars($klant['klantNaam']) . "</option>";
            }
            ?>
        </select>
        <br>
        <label for="verkOrdDatum">Datum:</label>
        <input type="date" id="verkOrdDatum" name="verkOrdDatum" value="<?php echo isset($_POST['verkOrdDatum']) ? htmlspecialchars($_POST['verkOrdDatum']) : ''; ?>" required/>
        <br>
        <label for="verkOrdBestAantal">Bestel Aantal:</label>
        <input type="number" id="verkOrdBestAantal" name="verkOrdBestAantal" placeholder="Bestel Aantal" value="<?php echo isset($_POST['verkOrdBestAantal']) ? htmlspecialchars($_POST['verkOrdBestAantal']) : ''; ?>" required/>
        <br>
        <label for="verkOrdStatus">Status:</label>
        <input type="text" id="verkOrdStatus" name="verkOrdStatus" placeholder="Status" value="<?php echo isset($_POST['verkOrdStatus']) ? htmlspecialchars($_POST['verkOrdStatus']) : ''; ?>" required/>
        <br><br>
        <input type="submit" name="insert" value="Toevoegen">
    </form><br>

    <a href="read.php">Terug</a>

</body>
</html>