<?php
include 'includes/session.php';
$conn = $pdo->open();

$output = ['error' => false];

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$duration_days = isset($_POST['duration_days']) ? intval($_POST['duration_days']) : 1;

if ($id <= 0) {
    $output['error'] = true;
    $output['message'] = "ID de producto inválido.";
    echo json_encode($output);
    exit;
}

try {
    // Verificar que el producto existe
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();

    if (!$product) {
        $output['error'] = true;
        $output['message'] = "Producto no encontrado.";
        echo json_encode($output);
        exit;
    }

    if (isset($_SESSION['user'])) {
        // Usuario logueado: guardamos en DB
        // Primero verificamos si ya existe el producto en carrito
        $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute(['user_id' => $user['id'], 'product_id' => $id]);
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            // Actualizar cantidades (sumar al existente)
            $new_quantity = $cart_item['quantity'] + $quantity;
            $new_days = $duration_days; // Actualizamos días con el enviado
            $stmt = $conn->prepare("UPDATE cart SET quantity = :quantity, duration_days = :duration_days WHERE id = :cart_id");
            $stmt->execute([
                'quantity' => $new_quantity,
                'duration_days' => $new_days,
                'cart_id' => $cart_item['id']
            ]);
        } else {
            // Insertar nuevo registro
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, duration_days) VALUES (:user_id, :product_id, :quantity, :duration_days)");
            $stmt->execute([
                'user_id' => $user['id'],
                'product_id' => $id,
                'quantity' => $quantity,
                'duration_days' => $duration_days
            ]);
        }

        $output['message'] = "Producto agregado al carrito.";
    } else {
        // Usuario no logueado: guardamos en $_SESSION['cart']
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        // Buscar si el producto ya está en carrito
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['productid'] == $id) {
                $item['quantity'] += $quantity; // sumamos cantidad
                $item['duration_days'] = $duration_days; // actualizamos días
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $_SESSION['cart'][] = [
                'productid' => $id,
                'quantity' => $quantity,
                'duration_days' => $duration_days
            ];
        }

        $output['message'] = "Producto agregado al carrito (sesión).";
    }
} catch (PDOException $e) {
    $output['error'] = true;
    $output['message'] = $e->getMessage();
}

$pdo->close();
echo json_encode($output);
