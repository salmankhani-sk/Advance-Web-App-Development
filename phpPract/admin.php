<?php
$host = "127.0.0.1";
$db = "demo_forms";
$user = "root";
$pass = "";
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo "<tr><td colspan='7' style='color:red'>DB Connection Failed.</td></tr>";
    exit;
}

$sql = "SELECT id, full_name, email, gender, interests, country, created_at
        FROM users
        ORDER BY created_at DESC";

try {
    $stmt = $pdo->query($sql);

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['full_name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['gender']}</td>
                    <td>{$row['interests']}</td>
                    <td>{$row['country']}</td>
                    <td>{$row['created_at']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No record</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='7' style='color:red'>Query failed.</td></tr>";
}
?>