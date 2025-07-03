<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once 'includes/conn.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = 'Debes iniciar sesión.';
    header('location: profile.php');
    exit();
}

if (!isset($_GET['reservation_id']) || !isset($_GET['order_id'])) {
    $_SESSION['error'] = 'Datos incompletos del pago.';
    header('location: profile.php');
    exit();
}

$reservation_id = $_GET['reservation_id'];
$order_id = $_GET['order_id'];
$user_id = $_SESSION['user'];
$date = date('Y-m-d');

$conn = $pdo->open();

try {
    // Confirmamos que la reservación pertenece al usuario y no esté pagada
    $stmt = $conn->prepare("
        SELECT r.*, p.name AS product_name, u.email
        FROM reservations r
        JOIN products p ON r.product_id = p.id
        JOIN users u ON r.user_id = u.id
        WHERE r.id = :id AND r.user_id = :user_id AND r.status != 'paid'
    ");
    $stmt->execute(['id' => $reservation_id, 'user_id' => $user_id]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        $_SESSION['error'] = 'Reservación inválida o ya pagada.';
        header('location: profile.php');
        exit();
    }

    // Insertamos en tabla sales
    $stmt = $conn->prepare("INSERT INTO sales (user_id, pay_id, sales_date) VALUES (:user_id, :pay_id, :sales_date)");
    $stmt->execute(['user_id' => $user_id, 'pay_id' => $order_id, 'sales_date' => $date]);
    $sales_id = $conn->lastInsertId();

    // Marcamos reservación como pagada
    $update = $conn->prepare("UPDATE reservations SET status = 'paid' WHERE id = :id");
    $update->execute(['id' => $reservation_id]);

    // Enviamos correo de confirmación
    $email = $reservation['email'];
    $product = $reservation['product_name'];
    $message = "
        <h2>Gracias por tu pago.</h2>
        <p>Reservación confirmada: <strong>$product</strong></p>
        <p>ID de transacción: $order_id</p>
        <p>Fecha: $date</p>
        <a href='https://patagoniaviajes.infinityfreeapp.com/profile.php?sales_id=$sales_id'>Ver mi compra</a>
    ";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();                                     
        $mail->Host = 'smtp.gmail.com';                      
        $mail->SMTPAuth = true;                               
        $mail->Username = 'sanchuap@gmail.com';     
        $mail->Password = 'vksqwjxzbikzbfqo';                    
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );                         
        $mail->SMTPSecure = 'tls';                           
        $mail->Port = 587;                                   

        $mail->setFrom('sanchuap@gmail.com', 'PatagoniaViajes');
        $mail->addAddress($email);              
        $mail->addReplyTo('sanchuap@gmail.com');

        $mail->isHTML(true);                                  
        $mail->Subject = 'Confirmación de compra - PatagoniaViajes';
        $mail->Body    = $message;

        $mail->send();
        $_SESSION['success'] = 'Pago exitoso. Se ha enviado una confirmación por correo.';
    } catch (Exception $e) {

        // importante el correo se envia al correo de la cuenta del usuario registrado en el sistema no el que se pone en el de paypal
        $_SESSION['error'] = 'Pago registrado, pero no se pudo enviar el correo: ' . $mail->ErrorInfo;
    }

} catch (PDOException $e) {
    $_SESSION['error'] = 'Error en la base de datos: ' . $e->getMessage();
}

$pdo->close();
header('location: profile.php');
exit();
?>
