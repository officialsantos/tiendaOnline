<?php
include 'includes/session.php';
require_once 'includes/conn.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = 'Debes iniciar sesión.';
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
    $user_id = $_SESSION['user'];
    $reservation_id = $_POST['reservation_id'];

    $conn = $pdo->open();

    try {
        // Verificar que la reservación sea del usuario actual y esté pendiente
        $stmt = $conn->prepare("SELECT * FROM reservations WHERE id=:id AND user_id=:user_id AND status='pending'");
        $stmt->execute(['id' => $reservation_id, 'user_id' => $user_id]);

        if ($stmt->rowCount() > 0) {
            // Actualizar estado a cancelado
            $stmt = $conn->prepare("UPDATE reservations SET status='cancelled' WHERE id=:id");
            $stmt->execute(['id' => $reservation_id]);

            $_SESSION['success'] = "Reservación cancelada correctamente.";
        } else {
            $_SESSION['error'] = "No se encontró la reservación o ya fue procesada.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al cancelar: " . $e->getMessage();
    }

    $pdo->close();
} else {
    $_SESSION['error'] = "Solicitud inválida.";
}

header('Location: profile.php');
exit();
