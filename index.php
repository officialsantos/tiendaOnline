<?php
session_start();

// Productos en un array en lugar de base de datos
$productos = [
    ["id" => 1, "nombre" => "Licuadora Moulinex LM270558", "precio" => 10.500, "imagen" => "licuadora.jpg", "descripcion" => "Licuadora Optimix Plus Roja Moulinex LM270558: Lográ licuados perfectos con sus cuchillas de acero inoxidable, para mezclar, licuar y moler diversos ingredientes.Tiene una capacidad total de 2 litros y una potencia de 500 Watts."],
    ["id" => 2, "nombre" => "Licuadora Peabody 600W PE-LN601", "precio" => 73.950, "imagen" => "licuadora2.jpg", "descripcion" => "Licuadora de 600 watts de potencia.Perilla con control de 5 velocidades + función pulso.Cuchillas dentadas de acero inoxidable.Jarra de plástico de 1,5 Lts y resistente a amplias gamas de temperatura.Tapa dosificadora.Hasta 22.000 rpm."],
    ["id" => 3, "nombre" => "Licuadora con Vaso Mix & Go ", "precio" => 161.499, "imagen" => "licuadora3.jpg", "descripcion" => "Licuadora con Vaso Mix & Go 1,75 Litros Jarra Vidrio Pica Hielo 1000W SL-BL1402PN."],
    ["id" => 3, "nombre" => "Licuadora con Vaso Mix & Go ", "precio" => 161.499, "imagen" => "licuadora3.jpg", "descripcion" => "Licuadora con Vaso Mix & Go 1,75 Litros Jarra Vidrio Pica Hielo 1000W SL-BL1402PN."],
    ["id" => 1, "nombre" => "Licuadora Moulinex LM270558", "precio" => 10.500, "imagen" => "licuadora.jpg", "descripcion" => "Licuadora Optimix Plus Roja Moulinex LM270558: Lográ licuados perfectos con sus cuchillas de acero inoxidable, para mezclar, licuar y moler diversos ingredientes.Tiene una capacidad total de 2 litros y una potencia de 500 Watts."],
    ["id" => 2, "nombre" => "Licuadora Peabody 600W PE-LN601", "precio" => 73.950, "imagen" => "licuadora2.jpg", "descripcion" => "Licuadora de 600 watts de potencia.Perilla con control de 5 velocidades + función pulso.Cuchillas dentadas de acero inoxidable.Jarra de plástico de 1,5 Lts y resistente a amplias gamas de temperatura.Tapa dosificadora.Hasta 22.000 rpm."],
];


if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tienda Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function mostrarDescripcion(nombre, descripcion) {
            document.getElementById('modalTitulo').innerText = nombre;
            document.getElementById('modalDescripcion').innerText = descripcion;
            var descripcionModal = new bootstrap.Modal(document.getElementById('descripcionModal'));
            descripcionModal.show();
        }
    </script>
</head>
<body class="container">
    <h1 class="text-center mt-4">Tienda Online</h1>
    <div class="text-end mb-3">
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#carritoModal">Ver Carrito (<?= count($_SESSION["carrito"]) ?>)</button>
    </div>
    <div class="row">
        <?php foreach ($productos as $producto): ?>
            <div class="col-md-4 text-center">
                <div class="card p-3">
                    <img src="<?= $producto['imagen'] ?>" class="card-img-top img-fluid" style="object-fit: cover; cursor: pointer;" onclick="mostrarDescripcion('<?= $producto['nombre'] ?>', '<?= $producto['descripcion'] ?>')">
                    <div class="card-body">
                        <h5 class="card-title"><?= $producto['nombre'] ?></h5>
                        <p class="card-text">$<?= number_format($producto['precio'], 3, '.', ',') ?></p>
                        <a href="agregar_carrito.php?id=<?= $producto['id'] ?>" class="btn btn-primary">Agregar al carrito</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal de descripción -->
    <div class="modal fade" id="descripcionModal" tabindex="-1" aria-labelledby="descripcionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDescripcion"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal del Carrito -->
    <div class="modal fade" id="carritoModal" tabindex="-1" aria-labelledby="carritoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="carritoModalLabel">Carrito de Compras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (empty($_SESSION["carrito"])): ?>
                        <p>No hay productos en el carrito.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($_SESSION["carrito"] as $item): ?>
                                <li class="list-group-item"> <?= $item['nombre'] ?> - $<?= $item['precio'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <a href="carrito.php" class="btn btn-success">Finalizar Compra</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>