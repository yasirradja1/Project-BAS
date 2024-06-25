<!--
    Auteur: Yasir
    Functie: home page CRUD VerkoopOrder
-->
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerkoopOrder</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <h1>VerkoopOrder</h1>
    <nav>
        <a href='../index.html'>Home</a><br>
        <a href='insert.php'>Toevoegen verkoop order</a><br>
    </nav>
    
    <?php
    // Autoloader classes via composer
    require '../../vendor/autoload.php';

    use Bas\classes\VerkoopOrder;

    // Create a VerkoopOrder object
    $verkoopOrder = new VerkoopOrder();
    
    // Execute CRUD operations
    $verkoopOrder->crudVerkoopOrder();
    ?>
</body>
</html>