<?php
session_start();
$successProdukt = $_SESSION['success'] ?? null;
// add.php
require 'configDB.php';

$errors = [];
if($successProdukt != null){
  unset($_SESSION['success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validierung
    $bez = trim($_POST['bez'] ?? ''); //Leerzeichen und tabs die vor dem ersten Wort und nach dem letzten Wort stehen, werden entfernt
    $beschr = trim($_POST['beschr'] ?? '');
    $preis = trim($_POST['preis'] ?? '');
    $tierart = isset($_POST['tierart']) ? (int)$_POST['tierart'] : 0;

    if ($bez === '') $errors[] = "Bezeichnung ist erforderlich.";
    if ($preis === '' || !is_numeric($preis) || (float)$preis < 0) $errors[] = "Preis muss eine positive Zahl sein.";
    if (!in_array($tierart, [1,2,3])) $errors[] = "Tierart wählen (1=Hunde, 2=Katzen, 3=Maus).";

    if (empty($errors)) {
          // Id erzeugen (z.B. P + uniqid)
          $result = $con->query("SELECT id FROM produkt ORDER BY id DESC LIMIT 1");
          $row = $result->fetch_assoc();

          if ($row) {
              // Zahl aus ID extrahieren: P0008 → 8
              $num = (int)substr($row['id'], 1);
              $num++;
          } else {
              $num = 1; // Startwert
          }

          // Neue ID mit führenden Nullen
          $id = 'P' . str_pad($num, 4, '0', STR_PAD_LEFT); // P0009

          // SQL mit ? Platzhaltern und mit der $insrt Variable das Insert ausführen
          $sql = "INSERT INTO produkt (id, bez, beschr, preis, tierart) VALUES (?, ?, ?, ?, ?)";

          // insrt vorbereiten
          $insrt = $con->prepare($sql);
          if ($insrt === false) {
              die("Prepare fehlgeschlagen: " . $con->error);
          }

          // Parameter binden
          $insrt->bind_param("sssdi", $id, $bez, $beschr, $preis, $tierart);

          // Ausführen
          if ($insrt->execute()) {
              // Neues Produkt anzeigen
              $_SESSION['success'] = [
                  'id' => $id,
                  'bez' => $bez,
                  'beschr' => $beschr,
                  'preis' => (float)$preis,
                  'tierart' => $tierart
              ];

              header("Location: add.php");
              exit;
          } else {
              echo "Fehler beim Einfügen: " . $insrt->error;
          }

          // insrt schließen
          $insrt->close();
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neues Produkt anlegen</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body>
    <main class="container">
      <div class="search-wrap">
      <h1 id="profukt_anlegen">Neues Produkt anlegen</h1>

      <?php if ($errors): ?>
          <div class="errors">
              <ul>
                  <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
              </ul>
          </div>
      <?php endif; ?>

      <form method="post" action="add.php" class="add-form">
          <label for="bez">Bezeichnung</label>
          <input type="text" id="bez" name="bez" value="<?= htmlspecialchars($_POST['bez'] ?? '') ?>" required>

          <label for="beschr">Beschreibung</label>
          <input type="text" id="beschr" name="beschr" value="<?= htmlspecialchars($_POST['beschr'] ?? '') ?>">

          <label for="preis">Preis (z.B. 12.99)</label>
          <input type="text" id="preis" name="preis" value="<?= htmlspecialchars($_POST['preis'] ?? '') ?>" required>

          <label for="tierart">Tierart</label>
          <select name="tierart" id="tierart" required> <!-- Mit den required macht es das error array unbrauchbar -->
          <option value="">-- wählen --</option>
          <option value="1" <?= (isset($_POST['tierart']) && $_POST['tierart']==1) ? 'selected' : '' ?>>Hunde</option>
          <option value="2" <?= (isset($_POST['tierart']) && $_POST['tierart']==2) ? 'selected' : '' ?>>Katzen</option>
          <option value="3" <?= (isset($_POST['tierart']) && $_POST['tierart']==3) ? 'selected' : '' ?>>Maus</option>
          </select>

          <button type="submit">Produkt anlegen</button>
      </form>
      </div>              
      <?php if ($successProdukt != null): ?>
        <div class="success">
            <h2>Produkt angelegt</h2>
                <div class= "table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Bezeichnung</th>
                            <th>Beschreibung</th>
                            <th>Preis (€)</th>
                            <th>Tierart</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= htmlspecialchars($successProdukt['id']) ?></td>
                                <td><?= htmlspecialchars($successProdukt['bez']) ?></td>
                                <td><?= htmlspecialchars($successProdukt['bez']) ?></td>
                                <td><?= number_format($successProdukt['preis'], 2, ',', '.') ?></td>
                                <td><?= $successProdukt['tierart'] === 1 ? 'Hunde' ? $successProdukt['tierart'] === 2 :'Katzen' : 'Maus' ?></td>
                                <?php if($successProdukt): ?></td>
                                  <td>TRUE</td>
                                <?php endif; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
          </div>
      <?php endif; ?>
      <footer>
          <p><a href="index.php">Zur Suche</a></p>
          <p class="cpr">© Copyright</p>
      </footer> 
    </main>
</body>
</html>