<?php
// Knuutinen testaa PHP:tä Renderissä
// jos tää näkyy niin ope oli väärässä ja mä olin oikeessa 😎
// tässä on lomake + kellonaika vain todistamaan että PHP toimii
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>DeltaINC</title>
  <style>
    body{font-family:Arial, sans-serif;background:#1b1b1b;color:#eee;padding:40px}
    h1{color:#0f0}
    form{display:grid;gap:10px;max-width:400px;background:#222;padding:20px;border-radius:12px}
    input,textarea,button{padding:10px;border-radius:6px;border:1px solid #444;background:#111;color:#eee}
    button{cursor:pointer;font-weight:bold}
  </style>
</head>
<body>
  <h1>Tervetuloa tulevaisuuteen✅</h1>
  <p>Serverin kellonaika: <b><?php echo date("H:i:s"); ?></b></p>

  <h2>Lähetä viesti</h2>
  <form method="post" action="save.php">
    <label>Nimi: <input type="text" name="name" required></label>
    <label>Email: <input type="email" name="email" required></label>
    <label>Viesti:</label>
    <textarea name="message" rows="4" required></textarea>
    <button type="submit">Lähetä</button>
  </form>
</body>
</html>
