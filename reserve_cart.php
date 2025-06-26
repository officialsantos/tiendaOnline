<?php
include 'includes/session.php';
require_once 'includes/conn.php';

if(!isset($_SESSION['user'])){
    $_SESSION['error'] = 'Debes iniciar sesión para reservar.';
    header('Location: cart.php');
    exit();
}

// Como $_SESSION['user'] es el ID, lo usamos directamente
$user_id = $_SESSION['user'];

try {
    // Abrir conexión
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

    // Insertar cada ítem en la tabla de reservaciones
    $stmt_insert = $conn->prepare("INSERT INTO reservations (user_id, product_id, quantity, reserved_at, status, expire_at) VALUES (:user_id, :product_id, :quantity, NOW(), 'pending', DATE_ADD(NOW(), INTERVAL 2 DAY))");

    foreach ($cart_items as $item) {
        $stmt_insert->execute([
            'user_id' => $user_id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity']
        ]);
    }

    // Vaciar el carrito
    $stmt_delete = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $stmt_delete->execute(['user_id' => $user_id]);

    $pdo->close();

    $_SESSION['success'] = "¡Reserva creada! Tienes 2 días para pagar antes de que caduque.";
    header('Location: profile.php'); // Redirige a la página de reservas
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al crear la reserva: " . $e->getMessage();
    header('Location: profile.php');
    exit();
}
?>
