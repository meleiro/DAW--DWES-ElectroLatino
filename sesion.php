<?php
/**inspección y regeneración de sesión. */
require_once __DIR__ . '/includes/config.php';



if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {

    exigirPostValido();

    session_regenerate_id(true);

    volver('sesion.php');

}

cabecera('Inspección de sesión');
?>
<section class="intro">
    <p class="etiqueta">LABORATORIO</p><h1>¿Cómo funciona una sesión?</h1></section>
<div class="panel">
   
  <p> <strong>Nombre de la cookie de sesión: </strong> 
     <code class="romper"><?= e(session_name()) ?>  </code>
  </p>
  <p> <strong>Id de sesión: </strong> 
     <code class="romper"><?= e(session_id()) ?>  </code>  
  </p>
 <p>
        <strong>
            Contenido del carrito en <code>$_SESSION</code>:
        </strong>
    </p>

    <pre><?= e(print_r($_SESSION['carrito'], true)) ?></pre>

    <p>
        <strong>Unidades:</strong>
        <?= unidadesCarrito() ?>
    </p>

    <form method="post">
        <?= campoCsrf() ?>

        <button type="submit">
            Regenerar identificador de sesión
        </button>
    </form>

    <p class="nota">
        Prueba a abrir una ventana privada
        y compara los identificadores.
        No compartas ni publiques el ID de sesión.
    </p>

    <form action="cerrar.php" method="post" class="separado">
        <?= campoCsrf() ?>

        <button type="submit" class="peligro">
            Cerrar sesión y borrar carrito
        </button>
    </form>
    
</div>
<?php pie(); ?>
