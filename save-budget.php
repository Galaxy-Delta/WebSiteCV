<?php
// ===== SUOMEKSI: Salli CORS (vaihda origin omaan domainiin tuotannossa) =====
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

header('Content-Type: application/json; charset=utf-8');

// ===== SUOMEKSI: Lue ympäristömuuttujat Renderistä =====
$token  = getenv('GITHUB_TOKEN');
$repo   = getenv('GITHUB_REPO');   // esim. "galaxy-delta/WebSiteCV"
$branch = getenv('GITHUB_BRANCH') ?: 'main';

if (!$token || !$repo) {
  http_response_code(500);
  echo json_encode(['error' => 'Missing GITHUB_TOKEN or GITHUB_REPO']);
  exit;
}

// ===== SUOMEKSI: Lue JSON-runko =====
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
  http_response_code(400);
  echo json_encode(['error' => 'Bad JSON']);
  exit;
}

// ===== SUOMEKSI: Luo polku data/budgets/YYYY/MM/budget-<timestamp>.json =====
$now = new DateTime('now', new DateTimeZone('UTC'));
$yyyy = $now->format('Y');
$mm   = $now->format('m');
$ts   = preg_replace('/[:.]/', '-', $now->format(DateTime::ATOM));
$path = "data/budgets/$yyyy/$mm/budget-$ts.json";

// ===== SUOMEKSI: Valmistele GitHub API -pyyntö =====
list($owner, $reponame) = explode('/', $repo, 2);
$apiUrl = "https://api.github.com/repos/$owner/$reponame/contents/" . $path;

$content = base64_encode(json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
$payload = json_encode([
  'message'   => "budget: $ts",
  'content'   => $content,
  'branch'    => $branch,
  'committer' => ['name' => 'Delta Bot', 'email' => 'bot@delta.inc'],
  'author'    => ['name' => 'Delta Bot', 'email' => 'bot@delta.inc'],
], JSON_UNESCAPED_SLASHES);

// ===== SUOMEKSI: cURL pyyntö GitHubiin =====
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
  CURLOPT_CUSTOMREQUEST => 'PUT',
  CURLOPT_POSTFIELDS    => $payload,
  CURLOPT_RETURNTRANSFER=> true,
  CURLOPT_HTTPHEADER    => [
    "Authorization: Bearer $token",
    "User-Agent: DeltaINC-Budget-Client",
    "Content-Type: application/json"
  ]
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
  http_response_code(500);
  echo json_encode(['error' => "cURL error: $err"]);
  exit;
}

$resJson = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300) {
  $sha = $resJson['commit']['sha'] ?? null;
  echo json_encode(['ok' => true, 'path' => $path, 'commitSha' => $sha]);
} else {
  http_response_code($httpCode ?: 500);
  $msg = $resJson['message'] ?? 'Unknown error';
  echo json_encode(['error' => $msg, 'raw' => $resJson]);
}