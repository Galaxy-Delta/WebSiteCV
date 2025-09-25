<?php
declare(strict_types=1);
// SUOMEKSI: kevyt PHP vain serverin ajan näyttämiseen (todistus että PHP toimii)
$now = new DateTime('now', new DateTimeZone('Europe/Helsinki'));
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DeltaINC — demo</title>
  <meta name="color-scheme" content="dark light">
  <style>
    :root{
      /* SUOMEKSI: sävyt ja kiillon värit */
      --bg:#0b0b0b;
      --panel:#111213;
      --line:#2a2b2d;
      --text:#e9e9ea;
      --sub:#b9bcc1;
      --accent:#7aa7ff;
      --steel1:#9aa3ad; --steel2:#dfe3e7; --steel3:#c1c7cf; --steel4:#7c838c;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; background:
        radial-gradient(1200px 600px at 20% -10%, #232427 0%, #151618 40%, #0b0b0b 100%);
      color:var(--text);
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
      -webkit-font-smoothing:antialiased; text-rendering:optimizeLegibility;
      display:grid; place-items:center;
    }

    /* ===== SUOMEKSI: “metallinen” kehys ilman pyöristyksiä ===== */
    .frame{
      width:min(920px, 92vw);
      background: linear-gradient(180deg, #121316, #0d0e10);
      border:1px solid var(--line);
      outline:1px solid rgba(255,255,255,.04);
      border-radius:0;                /* ei pyöristyksiä */
      box-shadow:
        0 30px 60px rgba(0,0,0,.55),
        inset 0 1px 0 rgba(255,255,255,.05);
      padding:28px 22px 26px;
      position:relative; overflow:hidden;
    }
    /* kiiltoviiva “sweep” koko kehykseen */
    .frame::before{
      content:""; position:absolute; inset:-20%;
      background: linear-gradient(115deg, transparent 45%,
        rgba(255,255,255,.08) 50%, transparent 55%);
      transform: translateX(-120%);
      animation: sweep 4.6s ease-in-out infinite;
      pointer-events:none;
    }
    @keyframes sweep{
      0%{ transform:translateX(-120%) rotate(0.001deg) }
      65%{ transform:translateX(120%)  rotate(0.001deg) }
      100%{ transform:translateX(120%) rotate(0.001deg) }
    }

    /* ===== SUOMEKSI: Apple-tyyppinen “teräslogo” ===== */
    .logo{
      margin:0;
      font-weight:800;
      line-height:1;
      font-size:clamp(40px, 9vw, 86px);
      letter-spacing:.04em;
      background:
        conic-gradient(from 200deg at 50% 50%, var(--steel4), var(--steel2), var(--steel1), var(--steel3), var(--steel4));
      -webkit-background-clip:text; background-clip:text;
      color:transparent;
      position:relative;
      text-align:center;
      filter: drop-shadow(0 10px 28px rgba(0,0,0,.45));
    }
    /* kiiltävä “glare”-viiva logon päällä */
    .logo::after{
      content:""; position:absolute; inset:-8% -2%;
      background: linear-gradient(100deg, transparent 40%, rgba(255,255,255,.45) 50%, transparent 60%);
      mix-blend-mode:screen; opacity:.35;
      transform: translateX(-120%);
      animation: glare 3.4s ease-in-out infinite 0.6s;
      pointer-events:none;
    }
    @keyframes glare{
      0%{ transform:translateX(-120%) }
      60%{ transform:translateX(120%) }
      100%{ transform:translateX(120%) }
    }

    /* alaotsikko */
    .sub{
      margin:8px 0 0;
      text-align:center;
      color:var(--sub);
      font-size:clamp(14px, 2.1vw, 16px);
    }

    /* info-rivi */
    .row{
      display:flex; gap:12px; flex-wrap:wrap;
      justify-content:center;
      margin:18px 0 8px;
      color:#cfd3d8; font-variant-numeric:tabular-nums;
    }
    .chip{
      padding:10px 12px; border:1px solid var(--line); background:#0a0b0c; border-radius:0;
    }

    /* nappi: “Etusivulle” — moderni, neliöreunat */
    .actions{ display:flex; justify-content:center; margin-top:18px }
    .btn{
      appearance:none; border:1px solid #294b89; border-radius:0; cursor:pointer;
      background: linear-gradient(180deg, #6ea0ff, #3a6fe0);
      color:#081225; font-weight:700; letter-spacing:.02em;
      padding:14px 18px; min-width:180px; text-align:center; text-decoration:none;
      box-shadow: 0 8px 20px rgba(19,62,160,.35), inset 0 1px 0 rgba(255,255,255,.25);
      transition: transform .08s ease, filter .2s ease;
    }
    .btn:hover{ filter:brightness(1.02) }
    .btn:active{ transform: translateY(1px) }

    /* mobiilissa isommat pinnat */
    @media (max-width: 640px){
      .frame{ padding:22px 16px 20px }
      .btn{ width:100% }
    }
  </style>
</head>
<body>
  <main class="frame" role="main" aria-labelledby="app-title">
    <h1 id="app-title" class="logo">DeltaINC</h1>
    <p class="sub">Let's build the future together • This is my CV• PHP works! ✅</p>

    <div class="row" aria-label="palvelimen aika">
      <div class="chip">Server time: <strong><?= htmlspecialchars($now->format('H:i:s')) ?></strong></div>
      <div class="chip">Date: <strong><?= htmlspecialchars($now->format('Y-m-d')) ?></strong></div>
      <div class="chip">Timezone: <strong><?= htmlspecialchars($now->getTimezone()->getName()) ?></strong></div>
    </div>

    <div class="actions">
      <a class="btn" href="/OmaWEB.html" aria-label="Siirry etusivulle">← FrontPage</a>
    </div>
  </main>
</body>
</html>
