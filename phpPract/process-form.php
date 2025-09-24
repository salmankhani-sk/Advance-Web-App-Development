<?php
// 1. Connect to DB (adjust credentials!)
$host = "127.0.0.1";
$db   = "demo_forms";
$user = "root";
$pass = "";
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    exit("DB connection failed.");
}

// 2. Validate inputs
$full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS));
$email     = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$bio       = trim(strip_tags($_POST['bio'] ?? ''));
$gender    = $_POST['gender'] ?? 'other';
$country   = $_POST['country'] ?? '';
$tos       = isset($_POST['agreed_tos']) ? 1 : 0;

// interests[] array -> comma string
$allowedInterests = ['web','ai','ds'];
$interests = array_intersect($allowedInterests, $_POST['interests'] ?? []);
$interests_set = implode(',', $interests);

// required checks
$errors = [];
if ($full_name === '' || strlen($full_name) > 100) $errors[] = "Name required (max 100).";
if (!$email) $errors[] = "Valid email required.";
if ($country === '') $errors[] = "Country required.";
if ($tos !== 1) $errors[] = "Must accept Terms.";

if ($errors) {
    echo "<h3>Errors:</h3><ul>";
    foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>";
    echo "</ul><a href='form.html'>Go back</a>";
    exit;
}

// 3. Insert safely (prepared stmt avoids SQL injection)
$sql = "INSERT INTO users (full_name, email, bio, gender, interests, country, agreed_tos)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$full_name, $email, $bio, $gender, $interests_set, $country, $tos]);

echo "<h2>Thanks, $full_name!</h2><p>Your info has been saved.</p>";
