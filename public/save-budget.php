<?php
// ===== CORS (vaihda origin omaan domainiin tuotannossa) =====
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

header('Content-Type: application/json; charset=utf-8');

// ===== Env =====
$token  = getenv('GITHUB_TOKEN');
$repo   = getenv('GITHUB_REPO');   // muodossa "owner/repo"
$branch = getenv('GITHUB_BRANCH') ?: 'main';

if (!$token || !$repo) {
  http_response_code(500);
  echo json_encode(['error' => 'Missing GITHUB_TOKEN or GITHUB_REPO']); exit;
}

// ===== JSON body =====
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['error' => 'Bad JSON']); exit;
}

// ===== Kohdepolku =====
$now = new DateTime('now', new DateTimeZone('UTC'));
$yyyy = $now->format('Y');
$mm   = $now->format('m');
$ts   = preg_replace('/[:.]/', '-', $now->format(DateTime::ATOM));
$relPath = "data/budgets/$yyyy/$mm/budget-$ts.json";

/** SUOMEKSI: enkoodaa polun segmentit erikseen */
$segments = array_map('rawurlencode', explode('/', $relPath));
$encodedPath = implode('/', $segments);

// ===== GitHub API payload =====
[$owner, $reponame] = explode('/', $repo, 2);
$apiUrl = "https://api.github.com/repos/$owner/$reponame/contents/$encodedPath";

$content = base64_encode(json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
$payload = json_encode([
  'message'   => "budget: $ts",
  'content'   => $content,
  'branch'    => $branch,
  'committer' => ['name' => 'Delta Bot', 'email' => 'bot@delta.inc'],
  'author'    => ['name' => 'Delta Bot', 'email' => 'bot@delta.inc'],
], JSON_UNESCAPED_SLASHES);

// ===== cURL → GitHub =====
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
  CURLOPT_CUSTOMREQUEST  => 'PUT',
  CURLOPT_POSTFIELDS     => $payload,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER     => [
    "Authorization: Bearer $token",
    "User-Agent: DeltaINC-Budget-Client",
    "Accept: application/vnd.github+json",
    "Content-Type: application/json",
  ],
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err      = curl_error($ch);
curl_close($ch);

if ($err) {
  http_response_code(500);
  echo json_encode(['error' => "cURL error: $err"]); exit;
}

$res = json_decode($response, true);
if ($httpCode >= 200 && $httpCode < 300) {
  $sha = $res['commit']['sha'] ?? null;
  echo json_encode(['ok' => true, 'path' => $relPath, 'commitSha' => $sha]); exit;
}

http_response_code($httpCode ?: 500);
echo json_encode([
  'error' => $res['message'] ?? 'Unknown error',
  'docs'  => $res['documentation_url'] ?? null,
  'raw'   => $res,
]);
