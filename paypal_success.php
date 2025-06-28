<?php
include 'includes/session.php';
require_once 'includes/conn.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['reservation_id']) || !isset($_GET['order_id'])) {
    $_SESSION['error'] = 'Parámetros de pago inválidos.';
    header('Location: profile.php');
    exit();
}

$reservation_id = $_GET['reservation_id'];
$order_id = $_GET['order_id'];
$user_id = $_SESSION['user'];

$conn = $pdo->open();

try {
    // Obtener datos de reservación
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE id=:id AND user_id=:user_id");
    $stmt->execute(['id' => $reservation_id, 'user_id' => $user_id]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        $_SESSION['error'] = 'Reservación no encontrada.';
        header('Location: profile.php');
        exit();
    }

    // Obtener precio del producto
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :product_id");
    $stmt->execute(['product_id' => $reservation['product_id']]);
    $product = $stmt->fetch();

    $amount = $product['price'] * $reservation['quantity'];

    // Insertar en tabla sales
    $stmt = $conn->prepare("INSERT INTO sales (user_id, pay_id, sales_date) VALUES (:user_id, :pay_id, NOW())");
    $stmt->execute(['user_id' => $user_id, 'pay_id' => $order_id]);
    $sales_id = $conn->lastInsertId();

    // Insertar en tabla details
    $stmt = $conn->prepare("INSERT INTO details (sales_id, product_id, price, quantity) VALUES (:sales_id, :product_id, :price, :quantity)");
    $stmt->execute([
        'sales_id' => $sales_id,
        'product_id' => $reservation['product_id'],
        'price' => $product['price'],
        'quantity' => $reservation['quantity']
    ]);

    // Cambiar estado de reservación
    $stmt = $conn->prepare("UPDATE reservations SET status='completed' WHERE id=:id");
    $stmt->execute(['id' => $reservation_id]);

    $_SESSION['success'] = 'Pago realizado correctamente. Reservación confirmada.';
    header('Location: profile.php');
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al procesar el pago: ' . $e->getMessage();
    header('Location: profile.php');
    exit();
}

$pdo->close();
?>
