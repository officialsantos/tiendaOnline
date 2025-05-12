<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carrito</title>
</head>
<body>
    <h2>Carrito de Compras</h2>
    <?php if (empty($_SESSION["carrito"])): ?>
        <p>No hay productos en el carrito.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($_SESSION["carrito"] as $item): ?>
                <li><?= $item['nombre'] ?> - $<?= $item['precio'] ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <a href="index.php">Volver a la tienda</a>
</body>
</html>
