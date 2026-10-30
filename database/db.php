<?php
require_once __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO(SERVER_NAME, USER_NAME, PASSWORD);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    $dbError = null;
} 
catch (PDOException $e) {
    $pdo = null; 
    $dbError = $e->getMessage();

    error_log("Database Connection Failed: " . $dbError);
}