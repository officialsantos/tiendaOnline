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

    // Precio total ya calculado y guardado en la reservación
    $precio_total = $reservation['price']; // Usa el campo correcto

    // Insertar en tabla sales incluyendo el total pagado
    $stmt = $conn->prepare("INSERT INTO sales (user_id, pay_id, sales_date, total_paid) VALUES (:user_id, :pay_id, NOW(), :total_paid)");
    $stmt->execute([
        'user_id' => $user_id,
        'pay_id' => $order_id,
        'total_paid' => $precio_total
    ]);
    $sales_id = $conn->lastInsertId();

    // Insertar en tabla details, incluyendo duration_days y el precio tal cual
    $stmt = $conn->prepare("INSERT INTO details (sales_id, product_id, quantity, duration_days, price) VALUES (:sales_id, :product_id, :quantity, :duration_days, :price)");
    $stmt->execute([
        'sales_id' => $sales_id,
        'product_id' => $reservation['product_id'],
        'quantity' => $reservation['quantity'],
        'duration_days' => $reservation['duration_days'],
        'price' => $precio_total
    ]);

    // Actualizar estado de reservación a pagado
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
