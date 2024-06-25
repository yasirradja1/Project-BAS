<?php
// Auteur: Yasir
// Functie: Insert klantgegevens

require '../../vendor/autoload.php';
use Bas\classes\Klant;

if (isset($_POST["insert"]) && $_POST["insert"] === "Toevoegen") {
    if (!empty($_POST['klantnaam']) && !empty($_POST['klantemail']) && !empty($_POST['klantadres']) && !empty($_POST['klantpostcode']) && !empty($_POST['klantwoonplaats']) && !empty($_POST['klantwachtwoord'])) {
        // Maak een nieuw Klant object aan
        $klant = new Klant();
        
        // Bereid de klantgegevens voor
        $klantgegevens = [
            'klantNaam' => $_POST['klantnaam'],
            'klantEmail' => $_POST['klantemail'],
            'klantWoonplaats' => $_POST['klantwoonplaats'],
            'klantAdres' => $_POST['klantadres'],
            'klantPostcode' => $_POST['klantpostcode'],-
            'klantWachtwoord' => $_POST['klantwachtwoord']
        ];

        // Voeg de klantgegevens toe aan de database
        if ($klant->insertKlant($klantgegevens)) {
            echo "Klant succesvol toegevoegd!";
        } else {
            echo "Er is een fout opgetreden bij het toevoegen van de klant.";
        }
    } else {
        echo "Vul alstublieft alle vereiste velden in.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toevoegen Klant</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <h1>CRUD Klant</h1>
    <h2>Toevoegen</h2>
    <form method="post">
        <label for="klantnaam">Klantnaam:</label>
        <input type="text" id="klantnaam" name="klantnaam" placeholder="Klantnaam" required>
        <br>
        <label for="klantemail">Klantemail:</label>
        <input type="email" id="klantemail" name="klantemail" placeholder="Klantemail" required>
        <br>
        <label for="klantwachtwoord">KlantWachtwoord:</label>
        <input type="password" id="klantwachtwoord" name="klantwachtwoord" placeholder="Klantwachtwoord" required>
        <br>
        <label for="klantpostcode">KlantPostcode:</label>
        <input type="text" id="klantpostcode" name="klantpostcode" placeholder="Klantpostcode" required>
        <br>
        <label for="klantadres">KlantAdres:</label>
        <input type="text" id="klantadres" name="klantadres" placeholder="Klantadres" required>
        <br>
        <label for="klantwoonplaats">KlantWoonplaats:</label>
        <input type="text" id="klantwoonplaats" name="klantwoonplaats" placeholder="Klantwoonplaats" required>
        <br><br>
        <input type="submit" name="insert" value="Toevoegen">
    </form>
    <br>
    <a href="read.php">Terug</a>
</body>
</html>