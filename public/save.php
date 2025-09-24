declare(strict_types=1);

// ===== minne kirjoitan (publicin ulkopuolelle) =====
$target = __DIR__ . '/../data/submissions.json';

// ===== inputit talteen ja siistiksi =====
$clean = fn($s) => trim((string)$s);
$name    = mb_substr($clean($_POST['name']    ?? ''), 0, 120);
$email   = mb_substr($clean($_POST['email']   ?? ''), 0, 160);
$message = mb_substr($clean($_POST['message'] ?? ''), 0, 4000);

// kevyt email-tsekki
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $email = '';
}

// ===== varmista kansio =====
$dir = dirname($target);
if (!is_dir($dir)) { mkdir($dir, 0775, true); }

// ===== lataa vanha lista =====
$items = [];
if (is_file($target)) {
  $json  = file_get_contents($target);
  $items = json_decode($json, true) ?: [];
}

// ===== lisää uusi rivi =====
$items[] = [
  'name'    => $name,
  'email'   => $email,
  'message' => $message,
  'time'    => date('c'),
  'ip'      => $_SERVER['REMOTE_ADDR'] ?? null
];

// ===== kirjoita atomisesti =====
file_put_contents($target, json_encode($items, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), LOCK_EX);

// ===== Kiitos-sivu metallinhohdolla + automaattinen siirto OmaWEBiin =====
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Kiitos!</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- automaattinen ohjaus 2s päästä -->
  <meta http-equiv="refresh" content="2; url=/OmaWEB.html">
  <style>
    :root{color-scheme:dark}
    body{
      margin:0; min-height:100dvh; display:grid; place-items:center;
      background: radial-gradient(1200px 600px at 20% 0%, #3f3f3f 0%, #2a2a2a 35%, #171717 100%);
      font-family: system-ui, Arial, sans-serif; color:#eee;
    }
    .wrap{
      width:min(720px,92vw);
      background: linear-gradient(145deg,#2b2b2b,#1b1b1b);
      border:1px solid #3a3a3a; border-radius:18px; padding:26px;
      box-shadow: 0 10px 30px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.04);
      position:relative; overflow:hidden; text-align:center;
    }
    .wrap::before{
      content:""; position:absolute; inset:0; pointer-events:none;
      background: linear-gradient(120deg, transparent 0 40%, rgba(255,255,255,.06) 50%, transparent 60% 100%);
      transform: translateX(-100%); animation: shine 4.2s ease-in-out infinite;
    }
    @keyframes shine{0%{transform:translateX(-100%)}60%{transform:translateX(120%)}100%{transform:translateX(120%)}}
    h1{margin:0 0 8px; font-weight:800; letter-spacing:.3px}
    p{margin:6px 0 12px; opacity:.9}
    .btn{
      display:inline-block; margin-top:8px; padding:12px 16px; border-radius:12px;
      background: linear-gradient(180deg,#2e6df5,#1f57cc);
      border:1px solid #2a5dda; color:#fff; text-decoration:none; font-weight:700;
    }
    .hint{font-size:13px; opacity:.8; margin-top:6px}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Kiitos <?=htmlspecialchars($name ?: 'sinä')?> ✅</h1>
    <p>Sun viesti tallennettiin siististi.</p>
    <a class="btn" href="/OmaWEB.html">← Takaisin etusivulle nyt</a>
    <div class="hint">Ohjaan sinut etusivulle automaattisesti muutamassa sekunnissa…</div>
  </div>
</body>
</html>
