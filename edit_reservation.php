<?php
include 'includes/session.php';
require_once 'includes/conn.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = 'Debes iniciar sesión.';
    header('Location: login.php');
    exit();
}

$conn = $pdo->open();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['reservation_id'];
    $quantity = (int) $_POST['quantity'];
    $duration = (int) $_POST['duration_days'];
    $price = (float) $_POST['price']; // Nuevo: recibimos el total calculado
    $user_id = $_SESSION['user'];

    try {
        $stmt = $conn->prepare("
            SELECT p.category_id 
            FROM reservations r 
            JOIN products p ON p.id = r.product_id 
            WHERE r.id = :id AND r.user_id = :user_id AND r.status = 'pending'
        ");
        $stmt->execute(['id' => $id, 'user_id' => $user_id]);
        $res_cat = $stmt->fetch();

        if (!$res_cat) {
            $_SESSION['error'] = 'Reservación no encontrada o ya fue procesada.';
            $pdo->close();
            header('Location: profile.php');
            exit();
        }

        $max_quantity = 1;
        switch ($res_cat['category_id']) {
            case 1: $max_quantity = 1; break;
            case 2: $max_quantity = 7; break;
            case 3: $max_quantity = 20; break;
            default: $max_quantity = 1;
        }

        if ($quantity < 1 || $quantity > $max_quantity) {
            $_SESSION['error'] = "La cantidad debe estar entre 1 y $max_quantity según la categoría del paquete.";
            $pdo->close();
            header("Location: edit_reservation.php?id=$id");
            exit();
        }

        // Nuevo: actualizamos también el precio total
        $stmt = $conn->prepare("UPDATE reservations SET quantity=:quantity, duration_days=:duration, price=:price WHERE id=:id AND user_id=:user_id AND status='pending'");
        $stmt->execute([
            'quantity' => $quantity,
            'duration' => $duration,
            'price' => $price,
            'id' => $id,
            'user_id' => $user_id
        ]);

        $_SESSION['success'] = 'Reservación actualizada correctamente.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
    }

    $pdo->close();
    header('Location: profile.php');
    exit();

} else if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user'];

    try {
        $stmt = $conn->prepare("
            SELECT r.*, p.name, p.category_id, p.price AS base_price 
            FROM reservations r 
            JOIN products p ON p.id = r.product_id 
            WHERE r.id = :id AND r.user_id = :user_id AND r.status = 'pending'
        ");
        $stmt->execute(['id' => $id, 'user_id' => $user_id]);

        if ($stmt->rowCount() == 0) {
            $_SESSION['error'] = 'Reservación no encontrada o ya fue procesada.';
            header('Location: profile.php');
            exit();
        }

        $res = $stmt->fetch();

        $max_quantity = 1;
        switch ($res['category_id']) {
            case 1: $max_quantity = 1; break;
            case 2: $max_quantity = 7; break;
            case 3: $max_quantity = 20; break;
            default: $max_quantity = 1;
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error al buscar reservación: ' . $e->getMessage();
        header('Location: profile.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'ID inválido.';
    header('Location: profile.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <div class="content-wrapper">
        <div class="container">
            <section class="content">
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <h3 class="text-center">Editar Reservación</h3>

                        <?php
                        if(isset($_SESSION['error'])){
                            echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                            unset($_SESSION['error']);
                        }
                        if(isset($_SESSION['success'])){
                            echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
                            unset($_SESSION['success']);
                        }
                        ?>

                        <form method="POST" action="edit_reservation.php" id="reservationForm">
                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                            <input type="hidden" id="basePrice" value="<?php echo $res['base_price']; ?>">
                            <input type="hidden" name="price" id="finalPrice"> <!-- Nuevo campo -->

                            <div class="form-group">
                                <label>Paquete</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($res['name']); ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label>Cantidad</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" 
                                       value="<?php echo $res['quantity']; ?>" 
                                       min="1" max="<?php echo $max_quantity; ?>" required>
                                <small class="form-text text-muted">Máximo permitido: <?php echo $max_quantity; ?> personas</small>
                            </div>

                            <div class="form-group">
                                <label>Duración (días)</label>
                                <input type="number" name="duration_days" id="duration" class="form-control" value="<?php echo $res['duration_days']; ?>" min="1" required>
                            </div>

                            <div class="form-group">
                                <label>Total estimado:</label>
                                <p id="totalPrice" style="font-weight:bold; font-size:1.2em;">$ <?php echo number_format($res['price'], 2); ?></p>
                            </div>

                            <button type="submit" class="btn btn-success">Guardar Cambios</button>
                            <a href="profile.php" class="btn btn-default">Cancelar</a>
                        </form>

                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
    // Actualiza el precio total cuando cambian cantidad o duración
    document.addEventListener('DOMContentLoaded', function () {
        const quantityInput = document.getElementById('quantity');
        const durationInput = document.getElementById('duration');
        const totalPriceEl = document.getElementById('totalPrice');
        const basePrice = parseFloat(document.getElementById('basePrice').value);
        const finalPriceInput = document.getElementById('finalPrice');

        function updateTotal() {
            let quantity = parseInt(quantityInput.value) || 1;
            let duration = parseInt(durationInput.value) || 1;
            let total = basePrice * quantity * duration;
            totalPriceEl.textContent = '$ ' + total.toFixed(2);
            finalPriceInput.value = total.toFixed(2); // Seteamos el valor del input oculto
        }

        quantityInput.addEventListener('input', updateTotal);
        durationInput.addEventListener('input', updateTotal);

        updateTotal(); // Cargar el total al inicio
    });
</script>

</body>
</html>
