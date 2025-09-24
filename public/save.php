<?php
// Knuutinen edition - lomake data talteen JSONiin
// ope: "ei PHP voi toimia GitHubissa" -> minä: "kato nyt"

// tiedosto minne tallennetaan
$target = __DIR__ . '/../data/submissions.json';

// uusi entry
$data = [
  'name' => $_POST['name'] ?? '',
  'email' => $_POST['email'] ?? '',
  'message' => $_POST['message'] ?? '',
  'time' => date('c')
];

// luodaan data kansio jos ei ole
if (!is_dir(dirname($target))) {
  mkdir(dirname($target), 0775, true);
}

// lataa vanhat
$items = [];
if (file_exists($target)) {
  $json = file_get_contents($target);
  $items = json_decode($json, true) ?: [];
}

// lisätään uusi ja kirjoitetaan
$items[] = $data;
file_put_contents($target, json_encode($items, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Kiitos!</title>
</head>
<body style="font-family:Arial,sans-serif;background:#1b1b1b;color:#eee;padding:40px">
  <h1>Kiitos <?php echo htmlspecialchars($data['name']); ?> ✅</h1>
  <p>Sun viesti tallennettiin.</p>
  <a href="index.php">Takaisin lomakkeelle</a>
</body>
</html>
