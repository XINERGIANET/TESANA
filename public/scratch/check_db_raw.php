<?php
$host = '127.0.0.1';
$db   = 'u109698536_tesana';
$user = 'u109698536_tesana';
$pass = 'Tesana#2024';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     echo "--- Client Info ---\n";
     $stmt = $pdo->prepare("SELECT id, name, document, sessions FROM clients WHERE document = ?");
     $stmt->execute(['08804479']);
     $client = $stmt->fetch();
     if ($client) {
         print_r($client);
         
         echo "\n--- Attendances ---\n";
         $stmt = $pdo->prepare("SELECT id, date FROM attendances WHERE client_id = ? ORDER BY date DESC");
         $stmt->execute([$client['id']]);
         $attendances = $stmt->fetchAll();
         print_r($attendances);
     } else {
         echo "Client not found.\n";
     }

} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
