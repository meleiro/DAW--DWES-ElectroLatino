<?php
/** CARRITO: estructura HTML preparada para completar en clase. */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/productos.php';
cabecera('Tu carrito');
?>
<section class="intro">
    <p class="etiqueta">EL PROBLEMA DEL ESTADO</p>
    <h1>Tu carrito</h1>
    <p>Inicialmente no recordamos productos. Lo solucionaremos con sesiones PHP.</p>
</section>
<div class="panel">
    <p>Tu carrito está vacío.</p>
    <a class="boton" href="index.php">Ir al catálogo</a>
    
</div>
<?php pie(); ?>
