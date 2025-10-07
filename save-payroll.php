<?php
// CORS light
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if(!$input){
  http_response_code(400);
  echo json_encode(['error'=>'Invalid JSON']);
  exit;
}

$model = $input['model'] ?? null;
$pdfB64 = $input['pdf_base64'] ?? null;
$pdfName = $input['pdf_filename'] ?? null;

if(!$model || !$pdfB64 || !$pdfName){
  http_response_code(400);
  echo json_encode(['error'=>'Missing model/pdf']);
  exit;
}

// --- ENV ---
$token  = getenv('GH_TOKEN');
$repo   = getenv('GH_REPO');   // e.g. galaxy-delta/WebSiteCV
$branch = getenv('GH_BRANCH') ?: 'main';
$prefix = getenv('GH_PATH_PREFIX') ?: 'data/palkka';

if(!$token || !$repo){
  http_response_code(500);
  echo json_encode(['error'=>'GH env not configured']);
  exit;
}

// --- Build paths ---
$period = $model['period'] ?? date('Y-m');
$folder = $prefix . '/' . $period;

// tiedostot: model JSON + PDF
$stamp = date('Ymd_His');
$jsonName = "payroll_${period}_$stamp.json";
$pdfName  = preg_replace('~[^a-zA-Z0-9._-]~','_', $pdfName);

// payloadit GitHub Contents API:lle (base64-encoded content)
$jsonContent = json_encode([
  'meta'  => ['app'=>'DeltaINC PayCalculator','version'=>'1.0.0','saved_at'=>date('c')],
  'model' => $model
], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

function gh_put_file($token, $repo, $branch, $path, $contentB64){
  $url = "https://api.github.com/repos/$repo/contents/" . rawurlencode($path);
  $body = json_encode([
    'message' => "Add $path (auto)",
    'content' => $contentB64,
    'branch'  => $branch
  ]);
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'User-Agent: DeltaINC-Bot',
      "Authorization: token $token",
      'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => $body
  ]);
  $res = curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err = curl_error($ch);
  curl_close($ch);
  return [$code, $res, $err];
}

// Luo kansiota ei tarvitse erikseen — Contents API tekee polun tiedostolle.

// 1) JSON
list($c1, $r1, $e1) = gh_put_file(
  $token, $repo, $branch,
  "$folder/$jsonName",
  base64_encode($jsonContent)
);

// 2) PDF
list($c2, $r2, $e2) = gh_put_file(
  $token, $repo, $branch,
  "$folder/$pdfName",
  $pdfB64 // tämä on jo base64
);

// Ei vuoda UI:lle mitään speksejä; palautetaan vakio-OK
if($c1>=200 && $c1<300 && $c2>=200 && $c2<300){
  echo json_encode(['ok'=>true]);
} else {
  // silti palautetaan 200, koska tämän endpointin kuuluu olla "hiljainen onnistumisesta riippumatta"
  // mutta logitetaan Renderin lokeihin:
  error_log("GH save issue JSON=$c1 err=$e1; PDF=$c2 err=$e2");
  echo json_encode(['ok'=>true]);
}
