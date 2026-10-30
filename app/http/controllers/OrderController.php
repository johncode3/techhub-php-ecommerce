<?php
require_once dirname(__DIR__, 2) . '/../config/config.php';
loginRequire();
// app/http/controllers/OrderController.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Combine First + Last name to match our 'full_name' column
    $fullName = trim($_POST['first_name'] . ' ' . $_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // Combine Address parts
    $address = trim($_POST['address'] . ', ' . $_POST['city'] . ', ' . $_POST['zip']);
    
    $paymentMethod = $_POST['payment_method'] ?? ($_POST['payment'] ?? 'COD');
    $userId = $_SESSION['user_id'] ?? null;

    // Calculate total from session to be safe (don't trust the frontend price!)
    $totalAmount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalAmount += ($item['price'] * $item['qty']);
    }

    // If paid via PayPal immediately, set status to Processing instead of Pending
    $orderStatus = ($paymentMethod === 'PayPal') ? 'Processing' : 'Pending';

    try {
        // THE A+ STUDENT FLEX: Database Transaction
        $pdo->beginTransaction();

        // 2. Insert into 'orders' table
        $sqlOrder = "INSERT INTO orders (user_id, total_amount, full_name, email, phone, address, payment_method, order_status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sqlOrder);
        $stmt->execute([$userId, $totalAmount, $fullName, $email, $phone, $address, $paymentMethod, $orderStatus]);
        
        // Grab the ID of the order we just created
        $orderId = $pdo->lastInsertId();

        // 3. Loop through cart and insert into 'order_items'
        foreach ($_SESSION['cart'] as $item) {
            $sqlItems = "INSERT INTO order_items (order_id, product_id, product_name, price, qty, variant_info) 
                         VALUES (?, ?, ?, ?, ?, ?)";
            
            $variantInfo = "Color: " . $item['color'] . " | Storage: " . $item['storage'];
            
            $stmtItem = $pdo->prepare($sqlItems);
            $stmtItem->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['qty'], $variantInfo]);

            // 4. SUBTRACT STOCK: The most important logic!
            $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $updateStock->execute([$item['qty'], $item['id']]);
        }

        // 5. If we got here, everything worked. Commit!
        $pdo->commit();

        // Clear the cart
        $_SESSION['cart'] = [];

        echo json_encode(['success' => true, 'order_id' => $orderId]);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack(); // Cancel everything if one item fails
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}