<?php
/**
 *  añadir productos al carrito de la sesión.
 * De momento comprobamos que el formulario envía los datos por POST.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/productos.php';
exigirPostValido();
$id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || !isset($productos[$id])) {
    http_response_code(400);
    exit('Producto no válido.');
}

cabecera('Producto recibido');
?>
<section class="intro"><h1>Formulario recibido</h1></section>
<div class="panel">
    <p>El servidor ha recibido el producto <strong><?= e($productos[$id]['nombre']) ?></strong>.</p>
    <p><strong>Sin sesiones no lo estamos guardando para la siguiente petición.</strong></p>
    <a class="boton" href="carrito.php">Consultar el carrito</a>
</div>
<?php pie(); ?>
