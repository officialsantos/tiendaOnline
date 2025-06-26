<?php
include 'includes/session.php';
require_once 'includes/conn.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = 'Debes iniciar sesión para pagar.';
    header('Location: profile.php');
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Reservación no especificada.';
    header('Location: profile.php');
    exit();
}

$reservation_id = $_GET['id'];
$user_id = $_SESSION['user'];

$conn = $pdo->open();

try {
    // Consulta mejorada: traemos solo el nombre del producto, precio lo tomamos de la reserva
    $stmt = $conn->prepare("
        SELECT r.*, p.name AS product_name
        FROM reservations r 
        JOIN products p ON r.product_id = p.id 
        WHERE r.id = :id AND r.user_id = :user_id
    ");
    $stmt->execute(['id' => $reservation_id, 'user_id' => $user_id]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        $_SESSION['error'] = 'Reservación no encontrada.';
        header('Location: profile.php');
        exit();
    }

    if ($reservation['status'] === 'paid') {
        $_SESSION['error'] = 'Esta reservación ya fue pagada.';
        header('Location: profile.php');
        exit();
    }

    $duration_days = isset($reservation['duration_days']) && $reservation['duration_days'] > 0 ? $reservation['duration_days'] : 1;

    // Usamos el precio guardado en la reserva, no el de products
    $total = $reservation['price'] * $reservation['quantity'] * $duration_days;
    $product_name = $reservation['product_name'];

} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al cargar reservación: ' . $e->getMessage();
    header('Location: profile.php');
    exit();
}

$pdo->close();
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

<?php include 'includes/navbar.php'; ?>

<div class="content-wrapper">
  <div class="container">
    <section class="content">
      <div class="box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Pagar Reservación</h3>
        </div>
        <div class="box-body">
          <p><strong>Producto:</strong> <?php echo htmlspecialchars($product_name); ?></p>
          <p><strong>Cantidad:</strong> <?php echo (int)$reservation['quantity']; ?></p>
          <p><strong>Duración (días):</strong> <?php echo $duration_days; ?></p>
          <p><strong>Total a pagar:</strong> $<?php echo number_format($total, 2); ?></p>

          <!-- Contenedor PayPal -->
          <div id="paypal-button-container"></div>

          <!-- SDK PayPal -->
          <script src="https://www.paypal.com/sdk/js?client-id=TU_CLIENT_ID_AQUI&currency=USD"></script>
          <script>
            paypal.Buttons({
              createOrder: function(data, actions) {
                return actions.order.create({
                  purchase_units: [{
                    description: "Pago de reservación: <?= htmlspecialchars($product_name) ?>",
                    amount: {
                      value: '<?= number_format($total, 2, ".", "") ?>'
                    }
                  }]
                });
              },
              onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                  // Redirigir para registrar el pago
                  window.location = "paypal_success.php?reservation_id=<?= $reservation_id ?>&order_id=" + data.orderID;
                });
              }
            }).render('#paypal-button-container');
          </script>
        </div>
      </div>
    </section>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
</div>
</body>
</html>
