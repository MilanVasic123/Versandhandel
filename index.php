<?php
    //vami
    require 'configDB.php';

    // species aus GET oder default 0 (keine Auswahl)
    $species = isset($_GET['species']) ? (int)$_GET['species'] : 0;
    //echo "species: ".$species;
    $sort = isset($_GET['sort']) && in_array( $_GET['sort'],  ['asc','desc']) ? $_GET['sort'] : 'asc';

    // Wenn species ausgewählt (1 oder 2) -> Suche ausführen
    $produkte = [];
    $count = 0;
    $meldung = '';

    //zum Testen eine dritte Tierart eingefügt, um die Notiz zu testen 
    if ($species === 1 || $species === 2 || $species ===3) {
        $sql = "SELECT * FROM Produkt WHERE tierart = $species ORDER BY preis " . ($sort === 'asc' ? 'ASC' : 'DESC');
        $res = $con->query($sql);
            while($i = $res->fetch_assoc()){
        }
        $produkte = $res->num_rows;

        if ($produkte === 0) {
            $meldung = "Für die ausgewählte species wurden keine Produkte gefunden!";
        }else{
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Versandhandel für Hunde- und Katzenfutter</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <main class="container">
        <div class="search-wrap">
        <h1>Versandhandel für Hunde und Katzen</h1>
        <h2>Produktsuche:</h2>
        <form action="index.php" get="get" class="search">
            <label for="species">Tierart:</label>
            <select name="species" id="species" required> 
                <option value="">--Bitte wählen--</option>
                <option value="1" <?= $species === 1 ? 'selected' : '' ?>>Hunde</option>
                <option value="2" <?= $species === 2 ? 'selected' : '' ?>>Katzen</option>
                <option value="3" <?= $species === 3 ? 'selected' : '' ?>>Maus</option>
            </select>
            <label for="sort">Preis sortieren:</label>
            <select name="sort" id="sort">
                <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>aufsteigend</option>
                <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>absteigend</option>
            </select>

            <button type="submit">Suchen</button>
        </div>
            <section class="results">
                <?php
                    if($species === 1 || $species === 2 || $species ===3):
                ?>
                <?php
                    if($meldung):
                ?>
                <div class="notice"><strong><?= htmlspecialchars($meldung) ?></strong></div>
                <?php
                    else:
                ?>
                <div class="count">Gefundene Produkte: <strong><?= $produkte ?></strong></div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>Bezeichnung</th>
                                <th>Beschreibung</th>
                                <th>Preis (€)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($res as $p): ?>
                                <tr>
                                <td><?= htmlspecialchars($p['bez']) ?></td>
                                <td><?= htmlspecialchars($p['beschr']) ?></td>
                                <td><?= number_format($p['preis'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div> 
                <?php endif; ?>
                <?php endif; ?>
            </section>
        </form>
    <footer>
      <p><a href="add.php">Neues Produkt anlegen (Mitarbeitende)</a></p>
      <p class="cpr">© Copyright</p>
    </footer>
    </main>
</body>
</html>