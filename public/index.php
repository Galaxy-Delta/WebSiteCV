<?php
declare(strict_types=1);
// PHP antaa alkuarvon (todistus että backend toimii)
$now = new DateTime('now', new DateTimeZone('Europe/Helsinki'));
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DeltaINC</title>
  <meta name="color-scheme" content="dark light">
  <style>
    :root{
      --bg:#0b0b0b; --panel:#111213; --line:#2a2b2d;
      --text:#e9e9ea; --sub:#b9bcc1; --accent:#7aa7ff;
      --steel1:#9aa3ad; --steel2:#dfe3e7; --steel3:#c1c7cf; --steel4:#7c838c;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; background:radial-gradient(1200px 600px at 20% -10%, #232427 0%, #151618 40%, #0b0b0b 100%);
      color:var(--text); font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
      display:grid; place-items:center;
    }
    .frame{
      width:min(920px, 92vw); background:linear-gradient(180deg, #121316, #0d0e10);
      border:1px solid var(--line); outline:1px solid rgba(255,255,255,.04);
      border-radius:0; box-shadow:0 30px 60px rgba(0,0,0,.55), inset 0 1px 0 rgba(255,255,255,.05);
      padding:28px 22px 26px; position:relative; overflow:hidden;
    }
    .frame::before{
      content:""; position:absolute; inset:-20%;
      background:linear-gradient(115deg, transparent 45%, rgba(255,255,255,.08) 50%, transparent 55%);
      transform:translateX(-120%); animation:sweep 4.6s ease-in-out infinite;
    }
    @keyframes sweep{0%{transform:translateX(-120%)}65%{transform:translateX(120%)}100%{transform:translateX(120%)}}

    .logo{
      margin:0; font-weight:800; font-size:clamp(40px, 9vw, 86px); text-align:center;
      background:conic-gradient(from 200deg at 50% 50%, var(--steel4), var(--steel2), var(--steel1), var(--steel3), var(--steel4));
      -webkit-background-clip:text; background-clip:text; color:transparent;
      position:relative; filter:drop-shadow(0 10px 28px rgba(0,0,0,.45));
    }
    .logo::after{
      content:""; position:absolute; inset:-8% -2%;
      background:linear-gradient(100deg, transparent 40%, rgba(255,255,255,.45) 50%, transparent 60%);
      mix-blend-mode:screen; opacity:.35; transform:translateX(-120%);
      animation:glare 3.4s ease-in-out infinite 0.6s;
    }
    @keyframes glare{0%{transform:translateX(-120%)}60%{transform:translateX(120%)}100%{transform:translateX(120%)}}

    .sub{margin:8px 0 0; text-align:center; color:var(--sub);}
    .row{display:flex; gap:12px; flex-wrap:wrap; justify-content:center; margin:18px 0 8px;}
    .chip{padding:10px 12px; border:1px solid var(--line); background:#0a0b0c;}

    .actions{display:flex; justify-content:center; margin-top:18px}
    .btn{
      border:1px solid #294b89; border-radius:0; background:linear-gradient(180deg,#6ea0ff,#3a6fe0);
      color:#081225; font-weight:700; padding:14px 18px; min-width:180px; text-align:center; text-decoration:none;
      box-shadow:0 8px 20px rgba(19,62,160,.35), inset 0 1px 0 rgba(255,255,255,.25);
      transition:transform .08s ease, filter .2s ease;
    }
    .btn:hover{filter:brightness(1.02)}
    .btn:active{transform:translateY(1px)}
    @media(max-width:640px){.btn{width:100%}}
  </style>
</head>
<body>
  <main class="frame">
    <h1 class="logo">DeltaINC</h1>
    <p class="sub">Let's build the future together • This is my CV • PHP works! ✅</p>

    <div class="row">
      <div class="chip">Server time: <strong id="clock"><?= htmlspecialchars($now->format('H:i:s')) ?></strong></div>
      <div class="chip">Date: <strong id="date"><?= htmlspecialchars($now->format('Y-m-d')) ?></strong></div>
      <div class="chip">Timezone: <strong><?= htmlspecialchars($now->getTimezone()->getName()) ?></strong></div>
    </div>

    <div class="actions">
      <a class="btn" href="/OmaWEB.html">← FrontPage</a>
    </div>
  </main>

  <script>
    // JS päivittää kellon reaaliaikaisesti
    function updateClock(){
      const now = new Date();
      document.getElementById("clock").textContent = now.toLocaleTimeString("fi-FI",{hour12:false});
      document.getElementById("date").textContent  = now.toLocaleDateString("fi-FI");
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>
</body>
</html>
