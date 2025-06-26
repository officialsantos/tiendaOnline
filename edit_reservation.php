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
    $user_id = $_SESSION['user'];

    try {
        $stmt = $conn->prepare("UPDATE reservations SET quantity=:quantity, duration_days=:duration WHERE id=:id AND user_id=:user_id AND status='pending'");
        $stmt->execute([
            'quantity' => $quantity,
            'duration' => $duration,
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
        $stmt = $conn->prepare("SELECT r.*, p.name FROM reservations r JOIN products p ON p.id = r.product_id WHERE r.id=:id AND r.user_id=:user_id AND r.status='pending'");
        $stmt->execute(['id' => $id, 'user_id' => $user_id]);

        if ($stmt->rowCount() == 0) {
            $_SESSION['error'] = 'Reservación no encontrada o ya fue procesada.';
            header('Location: profile.php');
            exit();
        }

        $res = $stmt->fetch();

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
                        <form method="POST" action="edit_reservation.php">
                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">

                            <div class="form-group">
                                <label>Paquete</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($res['name']); ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label>Cantidad</label>
                                <input type="number" name="quantity" class="form-control" value="<?php echo $res['quantity']; ?>" min="1" required>
                            </div>

                            <div class="form-group">
                                <label>Duración (días)</label>
                                <input type="number" name="duration_days" class="form-control" value="<?php echo $res['duration_days']; ?>" min="1" required>
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
</body>
</html>
