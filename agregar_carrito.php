<?php
session_start();

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $productos = [
        ["id" => 1, "nombre" => "Producto 1", "precio" => 10.50, "imagen" => "imagen1.jpg"],
        ["id" => 2, "nombre" => "Producto 2", "precio" => 15.75, "imagen" => "imagen2.jpg"],
        ["id" => 3, "nombre" => "Producto 3", "precio" => 20.00, "imagen" => "imagen3.jpg"]
    ];

    foreach ($productos as $producto) {
        if ($producto["id"] == $id) {
            $_SESSION["carrito"][] = $producto;
            break;
        }
    }
}
header("Location: carrito.php");
?>