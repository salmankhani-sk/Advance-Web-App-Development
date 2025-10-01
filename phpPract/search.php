<?php
// --- 0) Basic helpers (very beginner-friendly) ---
function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// --- 1) Read query inputs (GET) ---
$id   = isset($_GET['id'])   ? trim($_GET['id'])   : '';
$name = isset($_GET['name']) ? trim($_GET['name']) : '';

// --- 2) Connect to DB (adjust if needed) ---
$host = "127.0.0.1";
$db   = "demo_forms";
$user = "root";
$pass = "";
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  $error = "DB connection failed.";
  $rows  = [];
}

// --- 3) Build a very simple, safe query ---
$error = $error ?? '';
$rows  = $rows  ?? [];

if ($error === '') {
  if ($id !== '' && ctype_digit($id)) {
    // Search by exact ID
    $sql = "SELECT id, full_name, email, gender, interests, country, created_at
            FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => (int)$id]);
    $rows = $stmt->fetchAll();
    $criteria = "ID = " . (int)$id;

  } elseif ($name !== '') {
    // Search by name (partial, case-insensitive)
    $sql = "SELECT id, full_name, email, gender, interests, country, created_at
            FROM users
            WHERE full_name LIKE :name
            ORDER BY created_at DESC
            LIMIT 50";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name' => "%{$name}%"]);
    $rows = $stmt->fetchAll();
    $criteria = "Name contains \"" . h($name) . "\"";

  } else {
    $criteria = "No criteria given";
    $rows = [];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Search Results</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap 5 CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
  >
</head>
<body class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Search Results</h1>
    <a class="btn btn-secondary" href="search.html">Back to Search</a>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= h($error) ?></div>
  <?php else: ?>
    <p class="text-muted">
      <strong>Criteria:</strong> <?= $criteria ? h($criteria) : '—' ?>
    </p>

    <?php if (empty($rows)): ?>
      <div class="alert alert-warning">No records found.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Full name</th>
              <th>Email</th>
              <th>Gender</th>
              <th>Interests</th>
              <th>Country</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= h($r['full_name']) ?></td>
                <td><?= h($r['email']) ?></td>
                <td><?= h($r['gender']) ?></td>
                <td><?= h($r['interests']) ?></td>
                <td><?= h($r['country']) ?></td>
                <td><?= h($r['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <p class="text-muted small mb-0"><?= count($rows) ?> record(s)</p>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</body>
</html>
