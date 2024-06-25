<?php
// Auteur: Yasir
// Functie: Insert verkoopordergegevens

require '../../vendor/autoload.php';
use Bas\classes\VerkoopOrder;
use Bas\classes\Artikel;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errorMsg = "";
    if (isset($_POST['insert']) && $_POST['insert'] == "Toevoegen") {
        // Controleer of alle velden zijn ingevuld
        if (
            !empty($_POST['klantId']) && 
            !empty($_POST['artId']) && 
            !empty($_POST['verkOrdDatum']) && 
            !empty($_POST['verkOrdBestAantal']) && 
            !empty($_POST['verkOrdStatus'])
        ) {
            // Maak een nieuw VerkoopOrder object aan
            $verkooporder = new VerkoopOrder();

            // Bereid de verkoopordergegevens voor
            $verkoopordergegevens = [
                'klantId' => htmlspecialchars($_POST['klantId']),
                'artId' => htmlspecialchars($_POST['artId']),
                'verkOrdDatum' => htmlspecialchars($_POST['verkOrdDatum']),
                'verkOrdBestAantal' => htmlspecialchars($_POST['verkOrdBestAantal']),
                'verkOrdStatus' => htmlspecialchars($_POST['verkOrdStatus'])
            ];

            // Voeg de verkoopordergegevens toe aan de database
            if ($verkooporder->insertVerkoopOrder($verkoopordergegevens)) {
                $successMsg = "Verkooporder succesvol toegevoegd!";
            } else {
                $errorMsg = "Er is een fout opgetreden bij het toevoegen van de verkooporder.";
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
    <title>Toevoegen Verkooporder</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <h1>CRUD Verkooporder</h1>
    <h2>Toevoegen</h2>

    <?php
    if (isset($errorMsg) && $errorMsg != "") {
        echo "<p style='color: red;'>$errorMsg</p>";
    } elseif (isset($successMsg) && $successMsg != "") {
        echo "<p style='color: green;'>$successMsg</p>";
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="klantId">Klant ID:</label>
        <input type="text" id="klantId" name="klantId" placeholder="Klant ID" value="<?php echo isset($_POST['klantId']) ? htmlspecialchars($_POST['klantId']) : ''; ?>" required/>
        <br>
        <label for="artId">Artikel:</label>
        <?php
            $artikel = new Artikel();
            $artikel->dropDownArtikel(isset($_POST['artId']) ? htmlspecialchars($_POST['artId']) : -1);
        ?>
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