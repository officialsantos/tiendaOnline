<?php
include 'includes/session.php';

$id = $_POST['id'];

$conn = $pdo->open();

$output = array('list' => '');

try {
    // Obtener info general de la venta (total_paid, pay_id, sales_date)
    $stmt = $conn->prepare("SELECT sales_date, pay_id, total_paid FROM sales WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $sale = $stmt->fetch();

    if (!$sale) {
        echo json_encode(['error' => 'Venta no encontrada']);
        exit();
    }

    // Obtener detalles, incluyendo el precio base del producto
    $stmt = $conn->prepare("
        SELECT details.*, products.name, products.price AS product_price 
        FROM details 
        LEFT JOIN products ON products.id = details.product_id 
        WHERE details.sales_id = :id
    ");
    $stmt->execute(['id' => $id]);

    $output['transaction'] = $sale['pay_id'];
    $output['date'] = date('M d, Y', strtotime($sale['sales_date']));

    foreach($stmt as $row){
        // Precio base del producto
        $price_base = $row['product_price'];

        // Precio guardado en details (subtotal individual para ese detalle)
        $subtotal = $row['price'];

        $output['list'] .= "
            <tr class='prepend_items'>
                <td>".htmlspecialchars($row['name'])."</td>
                <td>&#36; ".number_format($price_base, 2)."</td>
                <td>".(int)$row['quantity']."</td>
                <td>".(int)$row['duration_days']."</td>
                <td>&#36; ".number_format($subtotal, 2)."</td>
            </tr>	
        ";
    }

    // Total directo desde sales.total_paid
    $output['total'] = '<b>&#36; '.number_format($sale['total_paid'], 2).'</b>';

    $pdo->close();

    echo json_encode($output);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
