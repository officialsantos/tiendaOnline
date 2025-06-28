<?php
include 'includes/session.php';
require_once 'includes/conn.php';

if(!isset($_SESSION['user'])){
    $_SESSION['error'] = 'Debes iniciar sesión para reservar.';
    header('Location: cart.php');
    exit();
}

$user_id = $_SESSION['user'];

try {
    $conn = $pdo->open();

    // Obtener el carrito del usuario
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $cart_items = $stmt->fetchAll();

    if (count($cart_items) == 0) {
        $_SESSION['error'] = "Tu carrito está vacío.";
        header('Location: profile.php');
        exit();
    }

    $stmt_insert = $conn->prepare("INSERT INTO reservations (user_id, product_id, quantity, price, duration_days, reserved_at, status, expire_at) VALUES (:user_id, :product_id, :quantity, :price, :duration_days, NOW(), 'pending', DATE_ADD(NOW(), INTERVAL 2 DAY))");

    foreach ($cart_items as $item) {
        $duration_days = isset($item['duration_days']) ? $item['duration_days'] : 1;

        // Obtener precio actual del producto
        $stmt_price = $conn->prepare("SELECT price FROM products WHERE id = :product_id");
        $stmt_price->execute(['product_id' => $item['product_id']]);
        $product = $stmt_price->fetch();

        $price = $product ? $product['price'] : 0;

        $stmt_insert->execute([
            'user_id' => $user_id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'price' => $price,
            'duration_days' => $duration_days
        ]);
    }

    // Vaciar el carrito
    $stmt_delete = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $stmt_delete->execute(['user_id' => $user_id]);

    $pdo->close();

    $_SESSION['success'] = "¡Reserva creada! Tienes 2 días para pagar antes de que caduque.";
    header('Location: profile.php');
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al crear la reserva: " . $e->getMessage();
    header('Location: profile.php');
    exit();
}
?>
