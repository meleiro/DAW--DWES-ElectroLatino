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

    
</div>
<?php pie(); ?>
