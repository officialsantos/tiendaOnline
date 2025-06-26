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
    // Obtener datos de reservación (sin intentar obtener precio acá, eso se hace desde productos)
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE id=:id AND user_id=:user_id");
    $stmt->execute(['id' => $reservation_id, 'user_id' => $user_id]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        $_SESSION['error'] = 'Reservación no encontrada.';
        header('Location: profile.php');
        exit();
    }

    // Obtener precio y datos del producto relacionado
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = :product_id");
    $stmt->execute(['product_id' => $reservation['product_id']]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['error'] = 'Producto no encontrado.';
        header('Location: profile.php');
        exit();
    }

    // Calcular monto total (cantidad * precio * duración días si existe)
    $duration_days = isset($reservation['duration_days']) && $reservation['duration_days'] > 0 ? $reservation['duration_days'] : 1;
    $amount = $product['price'] * $reservation['quantity'] * $duration_days;

    // Insertar en tabla sales
    $stmt = $conn->prepare("INSERT INTO sales (user_id, pay_id, sales_date) VALUES (:user_id, :pay_id, NOW())");
    $stmt->execute(['user_id' => $user_id, 'pay_id' => $order_id]);
    $sales_id = $conn->lastInsertId();

    // Insertar en tabla details con el precio correcto
    $stmt = $conn->prepare("INSERT INTO details (sales_id, product_id, price, quantity) VALUES (:sales_id, :product_id, :price, :quantity)");
    $stmt->execute([
        'sales_id' => $sales_id,
        'product_id' => $reservation['product_id'],
        'price' => $product['price'],
        'quantity' => $reservation['quantity']
    ]);

    // Actualizar estado de reservación a completed o paid
    $stmt = $conn->prepare("UPDATE reservations SET status='paid' WHERE id=:id");
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
