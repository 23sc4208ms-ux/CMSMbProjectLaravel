<?php
$host='zephyr.proxy.rlwy.net';
$port=16716;
$db='railway';
$user='root';
$pass='epUIZSLBMSmcCLcOROcuijBzbzmXnDKb';
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db}";
    $opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5];
    $pdo = new PDO($dsn, $user, $pass, $opts);
    echo "CONNECTED\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
