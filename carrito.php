
<?php
/**
 * ARCHIVO: carrito.php
 *
 * OBJETIVO:
 * Mostrar los productos almacenados en el carrito
 * de compra utilizando sesiones PHP.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/productos.php';

cabecera('Tu carrito');
?>

<section class="intro">
    <p class="etiqueta">ESTADO DEL CARRITO</p>
    <h1>Tu carrito</h1>
</section>

<?php if (empty($_SESSION['carrito'])): ?>

    <div class="panel">
        <p>Tu carrito está vacío.</p>

        <a class="boton" href="index.php">
            Ir al catálogo
        </a>
    </div>

<?php else: ?>

    <?php
    /**
     * =====================================================
     * ACTIVIDAD 1. CREAR LA TABLA DEL CARRITO
     * =====================================================
     *
     * Sabemos que el carrito contiene productos porque
     * hemos entrado en el bloque else.
     *
     * TAREA:
     *
     * 1. Crear un div con la clase "panel".
     *
     * 2. Dentro del div, crear una tabla HTML.
     *
     * 3. La tabla debe tener cinco columnas:
     *
     *    - Producto
     *    - Precio
     *    - Cantidad
     *    - Subtotal
     *    - Acción
     *
     * 4. Utilizar las etiquetas:
     *
     *    <table>
     *    <thead>
     *    <tr>
     *    <th>
     *    <tbody>
     *
     * PISTA:
     *
     * <thead> contiene los encabezados.
     * <tbody> contendrá las filas de productos.
     *
     * Las filas del tbody se generarán mediante PHP.
     */
    ?>


    <?php
    /**
     * =====================================================
     * ACTIVIDAD 2. RECORRER LOS PRODUCTOS DEL CARRITO
     * =====================================================
     *
     * El carrito está almacenado en:
     *
     * $_SESSION['carrito']
     *
     * Su estructura puede ser:
     *
     * [
     *     1 => 2,
     *     3 => 1
     * ]
     *
     * Esto significa:
     *
     * Producto 1: dos unidades.
     * Producto 3: una unidad.
     *
     * TAREA:
     *
     * 1. Crear un bucle foreach que recorra el carrito.
     *
     * 2. En cada iteración recuperar:
     *
     *    - El identificador del producto ($id).
     *    - La cantidad comprada ($cantidad).
     *
     * 3. Utilizar la sintaxis alternativa de PHP:
     *
     *    foreach (...):
     *
     *    endforeach;
     *
     * 4. Dentro del bucle generaremos una fila <tr>
     *    por cada producto.
     *
     * PISTA:
     *
     * foreach ($array as $clave => $valor)
     *
     * En nuestro caso, la clave es el ID
     * y el valor es la cantidad.
     */
    ?>


    <?php
    /**
     * =====================================================
     * ACTIVIDAD 3. COMPROBAR QUE EL PRODUCTO EXISTE
     * =====================================================
     *
     * Los identificadores del carrito hacen referencia
     * a los productos del catálogo.
     *
     * El catálogo está almacenado en:
     *
     * $productos
     *
     * TAREA:
     *
     * 1. Dentro del foreach, comprobar que el ID
     *    existe en el catálogo.
     *
     * 2. Si no existe, saltar a la siguiente
     *    iteración del bucle.
     *
     * 3. Si existe, recuperar los datos completos
     *    del producto y guardarlos en $producto.
     *
     * PISTAS:
     *
     * - isset() permite comprobar si existe una clave.
     *
     * - continue permite saltar a la siguiente
     *   iteración de un bucle.
     *
     * - Podemos acceder a un producto mediante:
     *
     *   $productos[$id]
     */
    ?>


    <?php
    /**
     * =====================================================
     * ACTIVIDAD 4. MOSTRAR LOS DATOS DEL PRODUCTO
     * =====================================================
     *
     * Dentro de cada fila de la tabla debemos mostrar:
     *
     * 1. Nombre del producto.
     *
     * 2. Precio unitario.
     *
     * 3. Cantidad seleccionada.
     *
     * 4. Subtotal del producto.
     *
     * 5. Botón para eliminarlo.
     *
     * TAREA:
     *
     * Crear las celdas <td> correspondientes.
     *
     * Para obtener los datos del producto:
     *
     * $producto['nombre']
     * $producto['precio']
     *
     * Para obtener la cantidad:
     *
     * $cantidad
     *
     * PISTAS:
     *
     * <?= ... ?> permite mostrar un valor en HTML.
     *
     * e() permite escapar texto antes de mostrarlo.
     *
     * euros() permite mostrar un importe
     * con formato monetario.
     *
     * Ejemplo:
     *
     * 25.5 -> 25,50 €
     */
    ?>


    <?php
    /**
     * =====================================================
     * ACTIVIDAD 5. CALCULAR EL SUBTOTAL
     * =====================================================
     *
     * Cada producto tiene un precio y una cantidad.
     *
     * El subtotal se calcula multiplicando:
     *
     * Precio unitario * cantidad
     *
     * EJEMPLO:
     *
     * Precio: 20 €
     * Cantidad: 3
     *
     * Subtotal: 60 €
     *
     * TAREA:
     *
     * 1. Obtener el precio del producto.
     *
     * 2. Multiplicarlo por la cantidad.
     *
     * 3. Mostrar el resultado en la columna Subtotal.
     *
     * 4. Utilizar euros() para dar formato al importe.
     *
     * PISTA:
     *
     * El operador de multiplicación en PHP es *.
     *
     * Puedes convertir la cantidad a entero
     * mediante (int).
     */
    ?>


    <?php
    /**
     * =====================================================
     * ACTIVIDAD 6. ACTUALIZAR LA CANTIDAD
     * =====================================================
     *
     * Queremos que el usuario pueda modificar
     * las unidades de cada producto.
     *
     * TAREA:
     *
     * Dentro de la columna Cantidad:
     *
     * 1. Crear un formulario HTML.
     *
     * 2. Enviarlo mediante POST a:
     *
     *    actualizar.php
     *
     * 3. Incorporar el token CSRF utilizando:
     *
     *    campoCsrf()
     *
     * 4. Añadir un campo oculto con:
     *
     *    name="producto_id"
     *
     *    Su valor será el identificador
     *    del producto actual.
     *
     * 5. Añadir un input numérico:
     *
     *    name="cantidad"
     *
     * 6. Mostrar como valor inicial
     *    la cantidad que ya tiene el carrito.
     *
     * 7. Establecer:
     *
     *    Mínimo: 0
     *    Máximo: 99
     *
     * 8. Añadir un botón "Actualizar".
     *
     * IMPORTANTE:
     *
     * El formulario solo envía los datos.
     *
     * La modificación de la sesión se realizará
     * posteriormente en actualizar.php.
     */
    ?>


    <?php
    /**
     * =====================================================
     * ACTIVIDAD 7. ELIMINAR UN PRODUCTO
     * =====================================================
     *
     * Cada fila debe incluir un botón que permita
     * eliminar ese producto del carrito.
     *
     * TAREA:
     *
     * 1. Crear un formulario independiente.
     *
     * 2. Enviarlo mediante POST a:
     *
     *    eliminar.php
     *
     * 3. Incluir el token CSRF.
     *
     * 4. Incluir un campo oculto con el ID
     *    del producto que queremos eliminar.
     *
     * 5. Añadir un botón con el texto:
     *
     *    Eliminar
     *
     * 6. Aplicar al botón la clase CSS:
     *
     *    peligro
     *
     * PREGUNTA:
     *
     * ¿Por qué necesitamos enviar el ID del producto
     * si el usuario solamente pulsa un botón?
     *
     * IMPORTANTE:
     *
     * No debemos eliminar el producto directamente
     * desde carrito.php.
     *
     * El formulario enviará los datos a eliminar.php.
     */
    ?>


    <?php
    /**
     * =====================================================
     * ACTIVIDAD 8. MOSTRAR EL TOTAL DEL CARRITO
     * =====================================================
     *
     * Una vez terminada la tabla, mostraremos
     * el importe total de la compra.
     *
     * El total es la suma de todos los subtotales.
     *
     * EJEMPLO:
     *
     * Ratón:    15 € x 2 = 30 €
     * Teclado:  40 € x 1 = 40 €
     *
     * TOTAL: 70 €
     *
     * TAREA:
     *
     * 1. Crear un párrafo con la clase:
     *
     *    total
     *
     * 2. Mostrar el texto "Total:".
     *
     * 3. Utilizar la función:
     *
     *    totalCarrito()
     *
     * 4. Esta función recibe como argumento
     *    el catálogo de productos.
     *
     * 5. Mostrar el resultado utilizando euros().
     *
     * PREGUNTA:
     *
     * ¿Qué ventaja tiene utilizar una función
     * para calcular el total en lugar de repetir
     * el cálculo en varias páginas?
     */
    ?>


    <?php
    /**
     * =====================================================
     * ACTIVIDAD 9. VACIAR TODO EL CARRITO
     * =====================================================
     *
     * Finalmente, añadiremos una opción
     * para eliminar todos los productos.
     *
     * TAREA:
     *
     * 1. Crear un formulario independiente.
     *
     * 2. Enviarlo mediante POST a:
     *
     *    vaciar.php
     *
     * 3. Incorporar el token CSRF.
     *
     * 4. Añadir un botón con el texto:
     *
     *    Vaciar carrito
     *
     * 5. Aplicar la clase CSS:
     *
     *    secundario
     *
     * PREGUNTA:
     *
     * ¿Por qué este formulario no necesita
     * enviar el identificador de un producto?
     *
     * IMPORTANTE:
     *
     * Este formulario todavía no vacía el carrito.
     *
     * La lógica se implementará en vaciar.php.
     */
    ?>


<?php endif; ?>

<?php pie(); ?>

