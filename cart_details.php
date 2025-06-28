<?php
include 'includes/session.php';
$conn = $pdo->open();

$output = ''; // Aquí se guarda el HTML que devolveremos

// Si el usuario está logueado sincronizamos carrito sesión con DB
if (isset($_SESSION['user'])) {
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $row) {
            // Verificar si el producto ya existe en DB carrito del usuario
            $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM cart WHERE user_id=:user_id AND product_id=:product_id");
            $stmt->execute(['user_id' => $user['id'], 'product_id' => $row['productid']]);
            $crow = $stmt->fetch();

            $duration_days = isset($row['duration_days']) ? $row['duration_days'] : 1;

            if ($crow['numrows'] < 1) {
                // Insertar producto en carrito DB
                $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, duration_days) VALUES (:user_id, :product_id, :quantity, :duration_days)");
                $stmt->execute([
                    'user_id' => $user['id'],
                    'product_id' => $row['productid'],
                    'quantity' => $row['quantity'],
                    'duration_days' => $duration_days
                ]);
            } else {
                // Actualizar cantidades en DB carrito
                $stmt = $conn->prepare("UPDATE cart SET quantity=:quantity, duration_days=:duration_days WHERE user_id=:user_id AND product_id=:product_id");
                $stmt->execute([
                    'quantity' => $row['quantity'],
                    'duration_days' => $duration_days,
                    'user_id' => $user['id'],
                    'product_id' => $row['productid']
                ]);
            }
        }
        unset($_SESSION['cart']); // Una vez sincronizado, limpiamos carrito sesión
    }

    try {
        $total = 0;
        // Obtener productos en carrito desde DB
        $stmt = $conn->prepare("SELECT cart.id AS cartid, products.name, products.price, products.photo, cart.quantity, cart.duration_days FROM cart LEFT JOIN products ON products.id=cart.product_id WHERE cart.user_id=:user");
        $stmt->execute(['user' => $user['id']]);
        foreach ($stmt as $row) {
            $image = !empty($row['photo']) ? 'images/' . $row['photo'] : 'images/noimage.jpg';
            $quantity = $row['quantity'];
            $duration_days = $row['duration_days'] ?? 1;
            $subtotal = $row['price'] * $quantity * $duration_days;
            $total += $subtotal;

            $output .= "
                <tr>
                    <td><button type='button' data-id='" . $row['cartid'] . "' class='btn btn-danger btn-flat cart_delete'><i class='fa fa-remove'></i></button></td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>&#36; " . number_format($row['price'], 2) . "</td>
                    <td>
                        <span id='qty_" . $row['cartid'] . "'>" . $quantity . "</span>
                    </td>
                    <td>
                        <span id='days_" . $row['cartid'] . "'>" . $duration_days . "</span>
                    </td>
                    <td>&#36; " . number_format($subtotal, 2) . "</td>
                </tr>
            ";
        }
        $output .= "
            <tr>
                <td colspan='5' align='right'><b>Total</b></td>
                <td><b>&#36; " . number_format($total, 2) . "</b></td>
            </tr>
        ";
    } catch (PDOException $e) {
        $output = "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
    }
} else {
    // Usuario no logueado: carrito en $_SESSION
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        $total = 0;
        foreach ($_SESSION['cart'] as $row) {
            $stmt = $conn->prepare("SELECT name, price, photo FROM products WHERE id=:id");
            $stmt->execute(['id' => $row['productid']]);
            $product = $stmt->fetch();

            if (!$product) continue; // Si no existe producto, saltar

            $image = !empty($product['photo']) ? 'images/' . $product['photo'] : 'images/noimage.jpg';
            $quantity = $row['quantity'];
            $duration_days = $row['duration_days'] ?? 1;
            $subtotal = $product['price'] * $quantity * $duration_days;
            $total += $subtotal;

            $output .= "
                <tr>
                    <td><button type='button' data-id='" . $row['productid'] . "' class='btn btn-danger btn-flat cart_delete'><i class='fa fa-remove'></i></button></td>
                    <td>" . htmlspecialchars($product['name']) . "</td>
                    <td>&#36; " . number_format($product['price'], 2) . "</td>
                    <td>
                        <span id='qty_" . $row['productid'] . "'>" . $quantity . "</span>
                    </td>
                    <td>
                        <span id='days_" . $row['productid'] . "'>" . $duration_days . "</span>
                    </td>
                    <td>&#36; " . number_format($subtotal, 2) . "</td>
                </tr>
            ";
        }
        $output .= "
            <tr>
                <td colspan='5' align='right'><b>Total</b></td>
                <td><b>&#36; " . number_format($total, 2) . "</b></td>
            </tr>
        ";
    } else {
        $output .= "
            <tr>
                <td colspan='6' align='center'>Carrito vacío</td>
            </tr>
        ";
    }
}

$pdo->close();

echo json_encode($output);
