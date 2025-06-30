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
    // Obtener info general de la venta (fecha y pay_id)
    $stmt = $conn->prepare("SELECT sales_date, pay_id FROM sales WHERE id = :id");
    $stmt->execute(['id' => $sales_id]);
    $sale = $stmt->fetch();

    if (!$sale) {
        echo json_encode(['error' => 'Venta no encontrada']);
        exit();
    }

    // Obtener detalles de la venta incluyendo duration_days
    $stmt = $conn->prepare("SELECT d.*, p.name 
                            FROM details d 
                            LEFT JOIN products p ON p.id = d.product_id 
                            WHERE d.sales_id = :id");
    $stmt->execute(['id' => $sales_id]);
    $details = $stmt->fetchAll();

    $list = '';
    $total = 0;

    foreach($details as $row){
        // Asumimos que price ya es el total por ese detalle
        $subtotal = $row['price']; 

        $total += $subtotal;

        $list .= "<tr>
                    <td>".htmlspecialchars($row['name'])."</td>
                    <td>$ ".number_format($row['price'], 2)."</td>
                    <td>".(int)$row['quantity']."</td>
                    <td>".(int)$row['duration_days']."</td>  <!-- Duración agregada -->
                    <td>$ ".number_format($subtotal, 2)."</td>
                  </tr>";
    }

    $data = [
        'date' => date('M d, Y', strtotime($sale['sales_date'])),
        'transaction' => htmlspecialchars($sale['pay_id']),
        'list' => $list,
        'total' => "$ ".number_format($total, 2)
    ];

    echo json_encode($data);

} catch(PDOException $e){
    echo json_encode(['error' => $e->getMessage()]);
}

$pdo->close();
?>
