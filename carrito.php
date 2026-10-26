
<?php

/**
 * ============================================================
 * ARCHIVO: carrito.php
 * PROYECTO: MiniTienda PHP
 * ============================================================
 *
 * OBJETIVO:
 *
 * Mostrar al usuario los productos que tiene
 * almacenados en su carrito de compra.
 *
 * Los datos del carrito se recuperan de la sesión
 * mediante la variable superglobal $_SESSION.
 *
 * En este archivo vamos a trabajar:
 *
 * 1. Inclusión de archivos PHP.
 * 2. Recuperación de datos de sesión.
 * 3. Generación dinámica de HTML.
 * 4. Condicionales if / else.
 * 5. Recorrido de arrays con foreach.
 * 6. Formularios HTML con método POST.
 * 7. Campos ocultos.
 * 8. Protección mediante tokens CSRF.
 * 9. Cálculo de subtotales y total.
 * 10. Reutilización de funciones.
 *
 * FUNCIONAMIENTO:
 *
 * Cuando el usuario entra en carrito.php:
 *
 * - PHP recupera su sesión.
 * - Consulta los productos del carrito.
 * - Comprueba si el carrito está vacío.
 * - Si está vacío, muestra un mensaje.
 * - Si contiene productos, genera una tabla.
 * - Permite actualizar cantidades.
 * - Permite eliminar productos.
 * - Permite vaciar el carrito completo.
 *
 * IMPORTANTE:
 *
 * Este archivo muestra información.
 *
 * Las modificaciones se realizan en otros
 * archivos PHP mediante formularios POST.
 */


/**
 * ============================================================
 * 1. CARGAR LA CONFIGURACIÓN
 * ============================================================
 *
 * require_once permite incorporar otro archivo PHP.
 *
 * __DIR__ contiene la ruta del directorio
 * donde se encuentra este archivo.
 *
 * El operador punto (.) concatena cadenas.
 *
 * Por tanto, construimos la ruta absoluta
 * hacia includes/config.php.
 *
 * Este archivo debe encargarse de iniciar
 * o recuperar la sesión PHP.
 *
 * También debe cargar las funciones comunes
 * e inicializar el carrito cuando sea necesario.
 *
 * Utilizamos require_once para evitar
 * incluir varias veces el mismo archivo.
 */
require_once __DIR__ . '/includes/config.php';


/**
 * ============================================================
 * 2. CARGAR EL CATÁLOGO
 * ============================================================
 *
 * Incorporamos el archivo productos.php.
 *
 * Contiene el array $productos, donde se guarda
 * la información de los artículos disponibles.
 *
 * Por ejemplo:
 *
 * $productos = [
 *     1 => [
 *         'nombre' => 'Ratón inalámbrico',
 *         'precio' => 15.00
 *     ],
 *     2 => [
 *         'nombre' => 'Teclado mecánico',
 *         'precio' => 45.00
 *     ]
 * ];
 *
 * ¿POR QUÉ NECESITAMOS EL CATÁLOGO?
 *
 * Porque en la sesión guardamos principalmente
 * identificadores y cantidades.
 *
 * Por ejemplo:
 *
 * $_SESSION['carrito'] = [
 *     1 => 2,
 *     2 => 1
 * ];
 *
 * El catálogo permite obtener el nombre
 * y el precio correspondiente a cada ID.
 *
 * De esta forma no necesitamos guardar
 * los precios en el carrito.
 */
require_once __DIR__ . '/includes/productos.php';


/**
 * ============================================================
 * 3. MOSTRAR LA CABECERA DE LA PÁGINA
 * ============================================================
 *
 * cabecera() es una función reutilizable
 * definida en includes/funciones.php.
 *
 * Recibe el título de la página.
 *
 * En este caso:
 *
 * 'Tu carrito'
 *
 * Esta función genera el HTML inicial:
 *
 * - <!doctype html>
 * - <html>
 * - <head>
 * - <title>
 * - <body>
 * - Cabecera y navegación.
 * - Apertura del elemento <main>.
 *
 * También puede mostrar el nombre del visitante
 * si existe una cookie con esa preferencia.
 *
 * Como cabecera() imprime directamente HTML,
 * después podemos continuar escribiendo
 * el contenido propio de carrito.php.
 */
cabecera('Tu carrito');


/**
 * Cerramos temporalmente PHP.
 *
 * La etiqueta ?> permite salir del modo PHP
 * y escribir HTML directamente.
 *
 * PHP enviará ese HTML al navegador.
 */
?>

<!--
    ============================================================
    4. SECCIÓN INTRODUCTORIA
    ============================================================

    <section> representa una sección temática
    dentro del documento HTML.

    class="intro" permite aplicar estilos CSS
    específicos a esta sección.

    Esta parte es contenido estático:
    siempre se muestra independientemente
    de que el carrito esté vacío o lleno.
-->
<section class="intro">

    <!--
        Texto de identificación de la sección.

        La clase etiqueta permite aplicarle
        un estilo visual desde CSS.
    -->
    <p class="etiqueta">
        ESTADO EN EL SERVIDOR
    </p>

    <!--
        Encabezado principal de la página.

        <h1> representa el título principal
        del contenido.
    -->
    <h1>Tu carrito</h1>

    <!--
        Explicación didáctica sobre las sesiones.

        Cuando el usuario recarga esta página,
        se realiza una nueva petición HTTP.

        PHP recupera los datos asociados
        a la sesión y puede volver a mostrar
        los productos seleccionados.

        HTTP no recuerda automáticamente
        las peticiones anteriores.

        Las sesiones permiten mantener
        información asociada a un usuario
        entre distintas peticiones.
    -->
    <p>
        Recarga esta página o vuelve al catálogo:
        las cantidades se mantienen durante la sesión.
    </p>

</section>


<?php

/**
 * ============================================================
 * 5. COMPROBAR SI EL CARRITO ESTÁ VACÍO
 * ============================================================
 *
 * Volvemos a PHP para ejecutar una condición.
 *
 * Utilizamos la estructura:
 *
 * if (condicion):
 *
 * Esta es la sintaxis alternativa de if.
 *
 * Resulta especialmente útil cuando mezclamos
 * bloques PHP con grandes fragmentos HTML.
 *
 * En lugar de escribir:
 *
 * if (condicion) {
 *     ...
 * }
 *
 * Podemos escribir:
 *
 * if (condicion):
 *     ...
 * endif;
 *
 * La función empty() comprueba si una variable
 * o expresión está vacía.
 *
 * En nuestro caso:
 *
 * empty($_SESSION['carrito'])
 *
 * Devuelve true si el carrito no contiene
 * ningún producto.
 *
 * Por ejemplo:
 *
 * $_SESSION['carrito'] = [];
 *
 * empty(...) devuelve true.
 *
 * Si tenemos:
 *
 * $_SESSION['carrito'] = [
 *     1 => 2
 * ];
 *
 * empty(...) devuelve false.
 *
 * IMPORTANTE:
 *
 * La sesión debe haberse iniciado previamente.
 *
 * En nuestro proyecto config.php debe garantizar
 * que $_SESSION['carrito'] sea un array.
 */
?>

<?php if (empty($_SESSION['carrito'])): ?>

    <!--
        ========================================================
        6. MOSTRAR EL CARRITO VACÍO
        ========================================================

        Este bloque HTML solamente se genera
        cuando la condición del if es verdadera.

        Es decir, cuando no hay productos
        almacenados en el carrito.
    -->

    <!--
        Contenedor visual.

        class="panel" permite aplicar
        los estilos definidos en CSS.
    -->
    <div class="panel">

        <!-- Mensaje informativo -->
        <p>
            Tu carrito está vacío.
        </p>

        <!--
            Enlace hacia el catálogo.

            <a> crea un hipervínculo.

            href="index.php" indica
            la página de destino.

            La clase boton permite darle
            apariencia de botón mediante CSS.

            Al pulsarlo, el navegador realiza
            una petición GET a index.php.
        -->
        <a class="boton" href="index.php">
            Ir al catálogo
        </a>

    </div>


<?php else: ?>

    <!--
        ========================================================
        7. MOSTRAR EL CARRITO CON PRODUCTOS
        ========================================================

        El bloque else se ejecuta cuando
        la condición del if es falsa.

        Por tanto, cuando el carrito contiene
        al menos una entrada.

        Aquí construiremos una tabla HTML
        con los productos seleccionados.
    -->

    <div class="panel">

        <!--
            ====================================================
            8. TABLA DEL CARRITO
            ====================================================

            <table> representa una tabla de datos.

            Utilizamos una tabla porque queremos
            presentar información organizada
            en filas y columnas.

            Cada fila representará un producto.
        -->
        <table>

            <!--
                <thead> contiene la cabecera
                de la tabla.

                <tr> representa una fila.

                <th> representa una celda
                de encabezado.
            -->
            <thead>

                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>

            </thead>

            <!--
                <tbody> contiene las filas
                con los datos de los productos.

                Estas filas se generarán
                dinámicamente utilizando PHP.
            -->
            <tbody>


            <?php

            /**
             * ==================================================
             * 9. RECORRER EL CARRITO CON FOREACH
             * ==================================================
             *
             * foreach permite recorrer los elementos
             * de un array.
             *
             * Su estructura general es:
             *
             * foreach ($array as $clave => $valor)
             *
             * En nuestro caso:
             *
             * $_SESSION['carrito']
             *
             * contiene los productos seleccionados.
             *
             * $id:
             *
             * Es la clave del array.
             * Representa el identificador del producto.
             *
             * $cantidad:
             *
             * Es el valor asociado a esa clave.
             * Representa las unidades seleccionadas.
             *
             * EJEMPLO:
             *
             * $_SESSION['carrito'] = [
             *     1 => 2,
             *     3 => 4
             * ];
             *
             * Primera iteración:
             *
             * $id = 1
             * $cantidad = 2
             *
             * Segunda iteración:
             *
             * $id = 3
             * $cantidad = 4
             *
             * En cada iteración generaremos
             * una fila HTML de la tabla.
             *
             * Utilizamos la sintaxis alternativa:
             *
             * foreach (...):
             *
             * y cerraremos con endforeach.
             */
            ?>

            <?php foreach ($_SESSION['carrito'] as $id => $cantidad): ?>


                <?php

                /**
                 * ==============================================
                 * 10. COMPROBAR QUE EL PRODUCTO EXISTE
                 * ==============================================
                 *
                 * Aunque el carrito contiene un identificador,
                 * debemos comprobar que ese producto
                 * sigue existiendo en el catálogo.
                 *
                 * isset($productos[$id])
                 *
                 * comprueba si existe una entrada
                 * para ese identificador.
                 *
                 * El operador ! niega el resultado.
                 *
                 * Por tanto:
                 *
                 * !isset($productos[$id])
                 *
                 * será true si el producto no existe.
                 *
                 * continue:
                 *
                 * Es una instrucción de control de bucles.
                 *
                 * Cuando se ejecuta, termina la iteración
                 * actual y pasa a la siguiente.
                 *
                 * En este caso:
                 *
                 * Si el producto no existe,
                 * no generamos una fila para él.
                 *
                 * IMPORTANTE:
                 *
                 * continue no elimina el producto
                 * del array de sesión.
                 *
                 * Simplemente evita mostrarlo
                 * durante esta iteración.
                 */
                ?>

                <?php if (!isset($productos[$id])) continue; ?>


                <?php

                /**
                 * ==============================================
                 * 11. RECUPERAR LOS DATOS DEL PRODUCTO
                 * ==============================================
                 *
                 * Ahora sabemos que el producto existe.
                 *
                 * Recuperamos sus datos desde el catálogo.
                 *
                 * Por ejemplo:
                 *
                 * $productos[1] = [
                 *     'nombre' => 'Ratón inalámbrico',
                 *     'precio' => 15.00
                 * ];
                 *
                 * Si $id = 1:
                 *
                 * $producto = $productos[$id];
                 *
                 * guardará ese array en $producto.
                 *
                 * Así podemos acceder cómodamente a:
                 *
                 * $producto['nombre']
                 *
                 * $producto['precio']
                 *
                 * No estamos copiando información
                 * desde el navegador.
                 *
                 * Recuperamos los datos originales
                 * definidos en el servidor.
                 */
                ?>

                <?php $producto = $productos[$id]; ?>


                <!--
                    ============================================
                    12. GENERAR UNA FILA DEL CARRITO
                    ============================================

                    Cada iteración del foreach genera
                    un elemento <tr>.

                    Por tanto, si hay tres productos
                    diferentes, se generarán tres filas.
                -->
                <tr>

                    <!--
                        ========================================
                        13. MOSTRAR EL NOMBRE DEL PRODUCTO
                        ========================================

                        <td> representa una celda de datos.

                        La expresión:

                      

                        es una forma abreviada de echo.

                        Permite imprimir el resultado
                        de una expresión PHP dentro del HTML.

                        $producto['nombre']

                        obtiene el nombre del catálogo.

                        e() es una función reutilizable
                        que utiliza htmlspecialchars().

                        Su finalidad es escapar el texto
                        antes de insertarlo en HTML.

                        Así evitamos que determinados
                        caracteres se interpreten
                        como etiquetas HTML.
                    -->
                    <td>
                        <?= e($producto['nombre']) ?>
                    </td>


                    <!--
                        ========================================
                        14. MOSTRAR EL PRECIO UNITARIO
                        ========================================

                        $producto['precio']

                        obtiene el precio desde el catálogo.

                        euros() es una función reutilizable
                        que utiliza number_format().

                        Convierte un número como:

                        1234.5

                        en una cadena como:

                        1.234,50 €

                        Esta función sirve para presentar
                        el importe al usuario.

                        Los cálculos se realizan con
                        los valores numéricos originales.
                    -->
                    <td>
                        <?= euros($producto['precio']) ?>
                    </td>


                    <!--
                        ========================================
                        15. FORMULARIO PARA ACTUALIZAR CANTIDAD
                        ========================================

                        Cada producto tiene su propio
                        formulario de actualización.

                        Este formulario permite modificar
                        el número de unidades del producto.
                    -->
                    <td>

                        <!--
                            action="actualizar.php"

                            Indica el archivo PHP
                            que procesará el formulario.

                            method="post"

                            Indica que los datos se enviarán
                            mediante una petición HTTP POST.

                            class="en-linea"

                            Es una clase CSS que permite
                            organizar visualmente el formulario.
                        -->
                        <form
                            action="actualizar.php"
                            method="post"
                            class="en-linea"
                        >

                            <!--
                                =================================
                                16. INCORPORAR EL TOKEN CSRF
                                =================================

                                campoCsrf() genera un input oculto
                                con el token de seguridad.

                                Por ejemplo:

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="TOKEN"
                                >

                                El token se almacena en la sesión.

                                Cuando se envíe el formulario,
                                actualizar.php comprobará
                                que el token recibido coincide
                                con el de la sesión.

                                Esto dificulta los ataques CSRF.
                            -->
                            <?= campoCsrf() ?>


                            <!--
                                =================================
                                17. IDENTIFICADOR DEL PRODUCTO
                                =================================

                                Este input es de tipo hidden.

                                No se muestra visualmente,
                                pero su valor se envía
                                junto con el formulario.

                                name="producto_id"

                                es el nombre que utilizará PHP
                                para recuperar el identificador.

                                value contiene el ID actual.

                                (int)$id convierte el valor
                                a un número entero.

                                IMPORTANTE:

                                Aunque el campo esté oculto,
                                el usuario puede modificarlo
                                mediante DevTools.

                                Por eso actualizar.php debe
                                validar el identificador recibido.
                            -->
                            <input
                                type="hidden"
                                name="producto_id"
                                value="<?= (int)$id ?>"
                            >


                            <!--
                                =================================
                                18. CAMPO PARA MODIFICAR CANTIDAD
                                =================================

                                type="number"

                                Indica que esperamos
                                una entrada numérica.

                                name="cantidad"

                                Es el nombre que recibirá PHP.

                                value

                                Muestra la cantidad actual
                                del producto en el carrito.

                                min="0"

                                Indica al navegador que
                                la cantidad mínima es cero.

                                max="99"

                                Indica que la cantidad máxima
                                permitida en el formulario es 99.

                                aria-label

                                Proporciona un nombre accesible
                                para el campo.

                                Incluimos el nombre del producto
                                para identificar a qué artículo
                                corresponde la cantidad.

                                IMPORTANTE:

                                min y max son restricciones
                                del formulario HTML.

                                No sustituyen la validación
                                que debe realizar actualizar.php
                                en el servidor.
                            -->
                            <input
                                type="number"
                                name="cantidad"
                                value="<?= (int)$cantidad ?>"
                                min="0"
                                max="99"
                                aria-label="Cantidad de <?= e($producto['nombre']) ?>"
                            >


                            <!--
                                =================================
                                19. BOTÓN DE ACTUALIZACIÓN
                                =================================

                                type="submit" indica que
                                el botón envía el formulario.

                                Al pulsarlo, el navegador envía:

                                - csrf_token
                                - producto_id
                                - cantidad

                                hacia actualizar.php.

                                Ese archivo validará los datos
                                y modificará $_SESSION['carrito'].
                            -->
                            <button type="submit">
                                Actualizar
                            </button>

                        </form>

                    </td>


                    <!--
                        ========================================
                        20. CALCULAR EL SUBTOTAL
                        ========================================

                        El subtotal representa el importe
                        correspondiente a un producto.

                        Se calcula multiplicando:

                        Precio unitario * cantidad.

                        Por ejemplo:

                        Precio: 15 €
                        Cantidad: 3

                        Subtotal:

                        15 * 3 = 45 €

                        En PHP utilizamos el operador *
                        para realizar multiplicaciones.

                        La expresión:

                        (int)$cantidad

                        convierte explícitamente
                        la cantidad a entero.

                        Después utilizamos euros()
                        para formatear el resultado.
                    -->
                    <td>
                        <?= euros(
                            $producto['precio'] * (int)$cantidad
                        ) ?>
                    </td>


                    <!--
                        ========================================
                        21. FORMULARIO PARA ELIMINAR EL PRODUCTO
                        ========================================

                        Este formulario permite eliminar
                        un producto del carrito.

                        Es independiente del formulario
                        utilizado para actualizar cantidades.

                        Al enviarlo, el navegador realiza
                        una petición POST a eliminar.php.
                    -->
                    <td>

                        <form
                            action="eliminar.php"
                            method="post"
                        >

                            <!--
                                Token CSRF para proteger
                                la operación de eliminación.
                            -->
                            <?= campoCsrf() ?>


                            <!--
                                Enviamos el identificador
                                del producto que queremos eliminar.

                                El archivo eliminar.php
                                utilizará este dato para localizar
                                la entrada correspondiente
                                dentro de $_SESSION['carrito'].
                            -->
                            <input
                                type="hidden"
                                name="producto_id"
                                value="<?= (int)$id ?>"
                            >


                            <!--
                                Botón para enviar el formulario.

                                class="peligro" permite aplicar
                                un estilo visual diferente
                                a las acciones destructivas.

                                El estilo CSS no determina
                                el comportamiento de PHP.
                            -->
                            <button
                                type="submit"
                                class="peligro"
                            >
                                Eliminar
                            </button>

                        </form>

                    </td>

                </tr>


            <?php

            /**
             * ==================================================
             * 22. FINALIZAR EL BUCLE FOREACH
             * ==================================================
             *
             * endforeach cierra el bucle foreach
             * que comenzamos anteriormente.
             *
             * PHP repetirá el contenido del bucle
             * por cada entrada del carrito.
             *
             * Cada iteración válida generará
             * una nueva fila HTML.
             */
            ?>

            <?php endforeach; ?>


            <!--
                Cerramos el cuerpo de la tabla.
            -->
            </tbody>

        <!--
            Cerramos la tabla HTML.
        -->
        </table>


        <!--
            ====================================================
            23. MOSTRAR EL TOTAL DEL CARRITO
            ====================================================

            Este párrafo muestra el importe total
            de todos los productos seleccionados.

            totalCarrito($productos)

            es una función reutilizable definida
            en includes/funciones.php.

            Recibe el catálogo de productos
            y consulta las cantidades almacenadas
            en $_SESSION['carrito'].

            Para cada producto calcula:

            Precio * cantidad.

            Después suma todos los subtotales.

            La función devuelve un float.

            Finalmente, euros() convierte
            ese número en una cadena
            con formato monetario.
        -->
        <p class="total">
            Total:
            <?= euros(totalCarrito($productos)) ?>
        </p>


        <!--
            ====================================================
            24. FORMULARIO PARA VACIAR EL CARRITO
            ====================================================

            Este formulario permite eliminar
            todos los productos del carrito.

            A diferencia de eliminar.php,
            que elimina un artículo concreto,
            vaciar.php elimina todas las entradas.

            Utilizamos POST porque la operación
            modifica información de la sesión.
        -->
        <form
            action="vaciar.php"
            method="post"
        >

            <!--
                Incluimos el token CSRF
                para proteger esta operación.
            -->
            <?= campoCsrf() ?>


            <!--
                Botón que envía el formulario.

                class="secundario" permite aplicar
                estilos específicos desde CSS.

                Al pulsarlo, vaciar.php procesará
                la petición y vaciará el carrito.
            -->
            <button
                type="submit"
                class="secundario"
            >
                Vaciar carrito
            </button>

        </form>

    </div>


<?php

/**
 * ============================================================
 * 25. FINALIZAR EL CONDICIONAL
 * ============================================================
 *
 * endif cierra la estructura if / else.
 *
 * Recordemos:
 *
 * if (empty($_SESSION['carrito'])):
 *
 *     Mostrar mensaje de carrito vacío.
 *
 * else:
 *
 *     Mostrar tabla de productos.
 *
 * endif;
 *
 * Solamente se genera una de las dos
 * alternativas durante cada petición.
 */
?>

<?php endif; ?>


<?php

/**
 * ============================================================
 * 26. MOSTRAR EL PIE DE PÁGINA
 * ============================================================
 *
 * pie() es una función reutilizable
 * definida en includes/funciones.php.
 *
 * Complementa a cabecera().
 *
 * cabecera() abre la estructura HTML.
 *
 * pie() la cierra.
 *
 * Entre otras cosas, pie() genera:
 *
 * - El cierre de main.
 * - El elemento footer.
 * - El cierre de body.
 * - El cierre de html.
 *
 * Gracias a estas funciones evitamos
 * repetir el mismo encabezado y pie
 * en todas las páginas de la tienda.
 */
pie();
