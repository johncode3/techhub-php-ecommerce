<?php
// 1. Setup the response type
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Grab the "Mail" from JS
    $id = (int)$_POST['product_id'];
    $qty = (int)$_POST['qty'];
    $color = $_POST['color'] ?? 'Default';
    $storage = $_POST['storage'] ?? 'Standard';

    // 3. Simple Check
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }

    // 4. Initialize Cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Create the Unique ID (e.g., "3-Red-128GB")
    $cartKey = $id . "-" . $color . "-" . $storage;

    // 5. Add item to the Session "Bag"
    if (isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey]['qty'] += $qty;
    } else {
        // Fetch name/price from DB so the cart page has data instantly
        $stmt = $pdo->prepare("SELECT name, price, main_image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch();

        $_SESSION['cart'][$cartKey] = [
            'id' => $id,
            'name' => $p['name'],
            'price' => (float)$p['price'],
            'image' => $p['main_image'],
            'color' => $color,
            'storage' => $storage,
            'qty' => $qty
        ];
    }
    
    // 6. Calculate the total quantity now in the cart
    $totalCount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalCount += $item['qty'];
    }

    // 7. Send the "Receipt" back to JS
    echo json_encode([
        'success' => true,
        'newCount' => $totalCount // We send the new number here!
    ]);
    exit;
}