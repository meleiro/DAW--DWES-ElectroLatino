<?php
/**
 *  añadir productos al carrito de la sesión.
 * De momento comprobamos que el formulario envía los datos por POST.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/productos.php';
exigirPostValido();
$id = filter_input(
    INPUT_POST, 
    'producto_id', 
    FILTER_VALIDATE_INT);

if ($id === false || $id === null || !isset($productos[$id])) {
    http_response_code(400);
    exit('Producto no válido.');
}



$actual = (int) ( $_SESSION['carrito'][$id] ?? 0 );

$_SESSION['carrito'][$id] = min($actual + 1, 99);

volver('carrito.php');
