header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

try {
  $raw = file_get_contents('php://input');
  if ($raw === false) throw new Exception('No input');
  $data = json_decode($raw, true);
  if (!$data) throw new Exception('Invalid JSON');

  $pdfB64 = $data['pdf_base64'] ?? '';
  $pdfName = $data['pdf_filename'] ?? ('Payroll_'.date('Y-m').'_'.time().'.pdf');
  if (!$pdfB64) throw new Exception('pdf_base64 missing');

  // Base dir -> data/palkka/YYYY-MM/
  $base = __DIR__ . '/data/palkka/' . date('Y-m');
  if (!is_dir($base) && !mkdir($base, 0775, true)) {
    throw new Exception('Failed to create base dir');
  }

  // Unique run dir
  $runDir = $base . '/' . date('Ymd_His') . '-' . bin2hex(random_bytes(3));
  if (!mkdir($runDir, 0775, true)) {
    throw new Exception('Failed to create run dir');
  }

  // Write payload.json (everything client sent)
  $payloadPath = $runDir . '/payload.json';
  file_put_contents($payloadPath, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

  // Write PDF
  $pdfPath = $runDir . '/' . preg_replace('/[^a-zA-Z0-9._-]/','_', $pdfName);
  $pdfBytes = base64_decode($pdfB64);
  if ($pdfBytes === false) throw new Exception('Failed to decode pdf_base64');
  file_put_contents($pdfPath, $pdfBytes);

  echo json_encode([
    'ok' => true,
    'saved_dir' => str_replace(__DIR__.'/', '', $runDir),
    'pdf_file' => basename($pdfPath),
  ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
