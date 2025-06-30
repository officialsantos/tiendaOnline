<?php
include 'includes/session.php';
require_once 'includes/conn.php';

if(!isset($_POST['id'])){
    echo json_encode(['error' => 'No se recibió ID de venta']);
    exit();
}

$sales_id = $_POST['id'];

$conn = $pdo->open();

try {
    // Obtener info general de la venta
    $stmt = $conn->prepare("SELECT sales_date, pay_id, total_paid FROM sales WHERE id = :id");
    $stmt->execute(['id' => $sales_id]);
    $sale = $stmt->fetch();

    if (!$sale) {
        echo json_encode(['error' => 'Venta no encontrada']);
        exit();
    }

    // Traer detalles y también el precio del producto
    $stmt = $conn->prepare("SELECT d.*, p.name, p.price AS unit_price
                            FROM details d
                            LEFT JOIN products p ON p.id = d.product_id
                            WHERE d.sales_id = :id");
    $stmt->execute(['id' => $sales_id]);
    $details = $stmt->fetchAll();

    $list = '';

    foreach($details as $row){
        $product_price = $row['unit_price'];
        $subtotal = $row['price']; // Total de ese ítem (ya cargado en la base)

        $list .= "<tr>
                    <td>".htmlspecialchars($row['name'])."</td>
                    <td>$ ".number_format($product_price, 2)."</td>
                    <td>".(int)$row['quantity']."</td>
                    <td>".(int)$row['duration_days']."</td>
                    <td>$ ".number_format($subtotal, 2)."</td>
                  </tr>";
    }

    $data = [
        'date' => date('M d, Y', strtotime($sale['sales_date'])),
        'transaction' => htmlspecialchars($sale['pay_id']),
        'list' => $list,
        'total' => "$ ".number_format($sale['total_paid'], 2)
    ];

    echo json_encode($data);

} catch(PDOException $e){
    echo json_encode(['error' => $e->getMessage()]);
}

$pdo->close();
?>
