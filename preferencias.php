
<?php

/**
 * ============================================================
 * ARCHIVO: preferencias.php
 * PROYECTO: MiniTienda PHP
 * ============================================================
 *
 * OBJETIVO:
 *
 * Permitir que el usuario guarde determinadas
 * preferencias de navegación mediante cookies.
 *
 * En nuestro proyecto almacenamos:
 *
 * 1. El tema visual de la página.
 *
 *    - claro
 *    - oscuro
 *
 * 2. El nombre del visitante.
 *
 *    Por ejemplo:
 *
 *    Ana
 *
 * A diferencia del carrito de compra,
 * que se almacena en $_SESSION,
 * estas preferencias se guardan en cookies
 * que conserva el navegador.
 *
 * DIFERENCIA FUNDAMENTAL:
 *
 * SESIÓN:
 *
 * Los datos del carrito se almacenan
 * en el servidor y se asocian a un
 * identificador de sesión.
 *
 * COOKIE:
 *
 * El navegador conserva un pequeño
 * dato que enviará al servidor en
 * posteriores peticiones compatibles
 * con su ámbito y atributos.
 *
 * En este ejemplo:
 *
 * Cookie tema:
 *
 * tema=oscuro
 *
 * Cookie visitante:
 *
 * visitante=Ana
 *
 * Las cookies de preferencias se crean
 * únicamente cuando el usuario solicita
 * guardarlas mediante el formulario.
 *
 * Su duración máxima configurada
 * es de 30 días desde que se guardan.
 *
 * También proporcionamos un formulario
 * para eliminarlas.
 *
 * IMPORTANTE:
 *
 * No debemos almacenar contraseñas,
 * datos de pago ni información sensible
 * en estas cookies.
 *
 * El usuario puede inspeccionar las cookies
 * desde las herramientas de desarrollo
 * del navegador.
 */


/**
 * ============================================================
 * 1. CARGAR LA CONFIGURACIÓN GENERAL
 * ============================================================
 *
 * require_once permite incluir y ejecutar
 * otro archivo PHP.
 *
 * En nuestro caso:
 *
 * includes/config.php
 *
 * Este archivo contiene la configuración
 * general de MiniTienda.
 *
 * Entre otras tareas:
 *
 * - Inicia o recupera la sesión.
 * - Prepara el token CSRF.
 * - Carga las funciones reutilizables.
 * - Configura los parámetros de sesión.
 * - Define la variable $https.
 *
 * __DIR__ es una constante mágica de PHP.
 *
 * Contiene la ruta absoluta del directorio
 * donde se encuentra preferencias.php.
 *
 * El operador punto (.) concatena cadenas.
 *
 * require_once evita incluir repetidamente
 * el mismo archivo durante la petición.
 */
require_once __DIR__ . '/includes/config.php';


/**
 * ============================================================
 * 2. COMPROBAR EL MÉTODO HTTP DE LA PETICIÓN
 * ============================================================
 *
 * Este archivo realiza dos funciones:
 *
 * A. Mostrar el formulario de preferencias.
 *
 * B. Procesar el formulario cuando se envía.
 *
 * Para distinguir ambos casos utilizamos
 * el método HTTP de la petición.
 *
 * Normalmente:
 *
 * GET:
 *
 * El usuario abre preferencias.php
 * y quiere consultar el formulario.
 *
 * POST:
 *
 * El usuario ha enviado uno de los
 * formularios para guardar o borrar cookies.
 *
 * $_SERVER es una variable superglobal
 * que contiene información sobre la petición
 * y el entorno del servidor.
 *
 * $_SERVER['REQUEST_METHOD']
 *
 * indica el método HTTP utilizado.
 *
 * Por ejemplo:
 *
 * 'GET'
 *
 * 'POST'
 *
 * OPERADOR ??
 *
 * Se denomina operador de fusión nula.
 *
 * La expresión:
 *
 * $_SERVER['REQUEST_METHOD'] ?? ''
 *
 * devuelve el método si está definido.
 *
 * Si no existe, devuelve una cadena vacía.
 *
 * OPERADOR ===
 *
 * Realiza una comparación estricta:
 *
 * compara valor y tipo.
 *
 * Si el método es POST, ejecutaremos
 * el bloque del condicional.
 *
 * Si es GET, saltaremos ese bloque
 * y mostraremos el formulario.
 */
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {


    /**
     * ========================================================
     * 3. VALIDAR LA PETICIÓN Y EL TOKEN CSRF
     * ========================================================
     *
     * Antes de crear, modificar o eliminar cookies,
     * comprobamos que la petición sea válida.
     *
     * exigirPostValido() es una función
     * reutilizable definida en funciones.php.
     *
     * Comprueba:
     *
     * - Que el método HTTP sea POST.
     * - Que el token CSRF recibido sea válido.
     *
     * CSRF significa:
     *
     * Cross-Site Request Forgery.
     *
     * Este ataque intenta provocar acciones
     * no deseadas aprovechando la sesión
     * de un usuario.
     *
     * Los formularios incluyen un token
     * generado por campoCsrf().
     *
     * El servidor comprueba que ese token
     * corresponde a la sesión actual.
     *
     * Si la validación falla,
     * la función detiene la ejecución.
     *
     * Si es correcta, continuamos.
     */
    exigirPostValido();


    /**
     * ========================================================
     * 4. IDENTIFICAR LA ACCIÓN SOLICITADA
     * ========================================================
     *
     * En esta página tenemos dos formularios.
     *
     * El primero permite guardar preferencias.
     *
     * El segundo permite borrar las cookies.
     *
     * Para distinguirlos, cada formulario
     * envía un campo llamado accion.
     *
     * Primer formulario:
     *
     * accion = guardar
     *
     * Segundo formulario:
     *
     * accion = borrar
     *
     * $_POST es una variable superglobal
     * que contiene los datos enviados
     * mediante POST.
     *
     * $_POST['accion']
     *
     * recupera el valor del campo accion.
     *
     * Utilizamos ?? para establecer
     * un valor alternativo.
     *
     * Si el campo accion no existe,
     * utilizamos 'guardar'.
     *
     * EJEMPLOS:
     *
     * accion = borrar
     *
     * $accion contendrá 'borrar'.
     *
     * accion = guardar
     *
     * $accion contendrá 'guardar'.
     *
     * Campo accion ausente:
     *
     * $accion contendrá 'guardar'.
     */
    $accion = $_POST['accion'] ?? 'guardar';


    /**
     * ========================================================
     * 5. CONFIGURAR LOS ATRIBUTOS DE LAS COOKIES
     * ========================================================
     *
     * Creamos un array asociativo llamado $opciones.
     *
     * Este array contiene los atributos que
     * utilizaremos al crear o eliminar cookies.
     *
     * Posteriormente lo pasaremos
     * a la función setcookie().
     *
     * Los atributos controlan aspectos como:
     *
     * - Fecha de caducidad.
     * - Ruta.
     * - Uso de HTTPS.
     * - Acceso desde JavaScript.
     * - Envío entre sitios.
     *
     * Vamos a explicar cada uno.
     */
    $opciones = [

        /**
         * ----------------------------------------------------
         * expires
         * ----------------------------------------------------
         *
         * Indica cuándo debe caducar la cookie.
         *
         * time() devuelve la fecha y hora actuales
         * como marca de tiempo Unix.
         *
         * Es el número de segundos transcurridos
         * desde el 1 de enero de 1970, UTC.
         *
         * Queremos que las cookies duren 30 días.
         *
         * Calculamos:
         *
         * 30 días
         *
         * 24 horas por día
         *
         * 60 minutos por hora
         *
         * 60 segundos por minuto
         *
         * Multiplicamos:
         *
         * 30 * 24 * 60 * 60
         *
         * Resultado:
         *
         * 2.592.000 segundos.
         *
         * Al sumarlos a time(), obtenemos
         * una fecha futura de caducidad.
         *
         * IMPORTANTE:
         *
         * Cada vez que se guardan preferencias,
         * se calcula una nueva fecha de caducidad.
         *
         * Por tanto, el plazo de 30 días
         * vuelve a comenzar desde ese guardado.
         */
        'expires' => time() + 30 * 24 * 60 * 60,


        /**
         * ----------------------------------------------------
         * path
         * ----------------------------------------------------
         *
         * Indica la ruta del sitio web
         * para la que se aplica la cookie.
         *
         * El valor:
         *
         * '/'
         *
         * significa que la cookie estará
         * disponible en todo el sitio web
         * dentro del dominio correspondiente.
         *
         * Así podrá utilizarse tanto
         * en index.php como en carrito.php
         * o preferencias.php.
         */
        'path' => '/',


        /**
         * ----------------------------------------------------
         * secure
         * ----------------------------------------------------
         *
         * Es un atributo booleano.
         *
         * Cuando vale true, indica que
         * la cookie debe enviarse mediante
         * conexiones seguras HTTPS,
         * con particularidades para localhost.
         *
         * Utilizamos la variable $https,
         * definida en config.php.
         *
         * En un entorno local con HTTP,
         * normalmente será false.
         *
         * En una aplicación publicada
         * mediante HTTPS será true.
         *
         * En producción es recomendable
         * utilizar siempre HTTPS.
         */
        'secure' => $https,


        /**
         * ----------------------------------------------------
         * httponly
         * ----------------------------------------------------
         *
         * Cuando vale true, impide que
         * JavaScript acceda a la cookie
         * mediante document.cookie.
         *
         * Esto ayuda a proteger su contenido
         * frente a determinadas situaciones
         * relacionadas con ataques XSS.
         *
         * No significa que el navegador
         * no pueda utilizar la cookie.
         *
         * El navegador seguirá enviándola
         * al servidor cuando corresponda.
         *
         * Tampoco significa que el usuario
         * no pueda inspeccionarla mediante
         * las herramientas de desarrollo.
         *
         * En nuestro proyecto, PHP puede
         * leerla utilizando $_COOKIE.
         */
        'httponly' => true,


        /**
         * ----------------------------------------------------
         * samesite
         * ----------------------------------------------------
         *
         * Este atributo controla determinados
         * contextos en los que una cookie
         * puede enviarse entre sitios.
         *
         * Valores habituales:
         *
         * Strict:
         *
         * Aplica restricciones más estrictas
         * a las peticiones entre sitios.
         *
         * Lax:
         *
         * Permite ciertos casos de navegación
         * entre sitios, manteniendo restricciones
         * en otros contextos.
         *
         * None:
         *
         * Permite el envío entre sitios
         * si se cumplen los requisitos
         * correspondientes, incluido Secure.
         *
         * En este proyecto utilizamos Lax.
         *
         * SameSite contribuye a reducir
         * determinados riesgos CSRF.
         *
         * No sustituye la validación
         * de tokens en los formularios.
         */
        'samesite' => 'Lax',

    ];


    /**
     * ========================================================
     * 6. COMPROBAR SI EL USUARIO QUIERE BORRAR
     * ========================================================
     *
     * Ahora decidimos qué operación realizar.
     *
     * Si $accion contiene 'borrar',
     * eliminaremos las cookies de preferencias.
     *
     * En caso contrario, procesaremos
     * los valores del formulario para guardarlos.
     *
     * Utilizamos === para comparar
     * estrictamente con la cadena 'borrar'.
     */
    if ($accion === 'borrar') {


        /**
         * ====================================================
         * 7. ESTABLECER UNA CADUCIDAD PASADA
         * ====================================================
         *
         * Para solicitar la eliminación
         * de una cookie utilizamos setcookie()
         * con una fecha de caducidad pasada.
         *
         * En lugar de una fecha futura,
         * indicamos:
         *
         * time() - 3600
         *
         * 3600 segundos equivalen a una hora.
         *
         * Por tanto, estamos indicando
         * una caducidad situada una hora
         * antes del momento actual.
         *
         * Modificamos únicamente la clave
         * expires del array $opciones.
         *
         * El resto de atributos permanecen.
         *
         * Es importante utilizar el nombre
         * correcto de la cookie y mantener
         * su ámbito, especialmente path
         * y domain, si se configuró.
         */
        $opciones['expires'] = time() - 3600;


        /**
         * ====================================================
         * 8. SOLICITAR EL BORRADO DE LA COOKIE TEMA
         * ====================================================
         *
         * setcookie() envía una cabecera HTTP
         * Set-Cookie al navegador.
         *
         * PRIMER ARGUMENTO:
         *
         * 'tema'
         *
         * Es el nombre de la cookie.
         *
         * SEGUNDO ARGUMENTO:
         *
         * ''
         *
         * Es una cadena vacía.
         *
         * TERCER ARGUMENTO:
         *
         * $opciones
         *
         * Contiene los atributos de la cookie.
         *
         * Como expires está en el pasado,
         * solicitamos que el navegador
         * deje de conservar esta cookie.
         *
         * IMPORTANTE:
         *
         * setcookie() no modifica directamente
         * la cookie almacenada en el navegador.
         *
         * Envía una instrucción en la respuesta HTTP.
         */
        setcookie('tema', '', $opciones);


        /**
         * ====================================================
         * 9. SOLICITAR EL BORRADO DE VISITANTE
         * ====================================================
         *
         * Realizamos la misma operación
         * con la cookie llamada visitante.
         *
         * Esta cookie contiene el nombre
         * opcional introducido por el usuario.
         *
         * Su eliminación no afecta
         * a la cookie de sesión PHPSESSID.
         *
         * Tampoco elimina los productos
         * del carrito almacenado en $_SESSION.
         */
        setcookie('visitante', '', $opciones);


    } else {


        /**
         * ====================================================
         * 10. RECIBIR EL TEMA VISUAL
         * ====================================================
         *
         * Si la acción no es 'borrar',
         * procesamos el guardado de preferencias.
         *
         * El formulario envía un campo
         * llamado tema.
         *
         * Sus valores previstos son:
         *
         * claro
         *
         * oscuro
         *
         * Recuperamos el valor desde $_POST.
         *
         * Si el campo no existe,
         * utilizamos 'claro' como valor
         * predeterminado.
         */
        $tema = $_POST['tema'] ?? 'claro';


        /**
         * ====================================================
         * 11. VALIDAR EL TEMA RECIBIDO
         * ====================================================
         *
         * No debemos aceptar cualquier valor
         * recibido desde el navegador.
         *
         * Queremos permitir únicamente:
         *
         * 'claro'
         *
         * 'oscuro'
         *
         * Para ello combinamos:
         *
         * is_string()
         *
         * in_array()
         *
         * El operador &&
         *
         * Y el operador ternario ? :
         *
         * PRIMERA COMPROBACIÓN:
         *
         * is_string($tema)
         *
         * Comprueba que el dato sea
         * una cadena de texto.
         *
         * SEGUNDA COMPROBACIÓN:
         *
         * in_array(
         *     $tema,
         *     ['claro', 'oscuro'],
         *     true
         * )
         *
         * Comprueba si el valor pertenece
         * al conjunto permitido.
         *
         * El tercer argumento true indica
         * que se utilizará comparación estricta.
         *
         * OPERADOR &&
         *
         * Significa AND lógico.
         *
         * Ambas condiciones deben cumplirse.
         *
         * OPERADOR TERNARIO:
         *
         * condicion ? valorSiVerdadero : valorSiFalso
         *
         * Si el tema es válido,
         * conservamos su valor.
         *
         * Si no es válido,
         * utilizamos 'claro'.
         *
         * EJEMPLOS:
         *
         * 'oscuro' -> 'oscuro'
         *
         * 'claro'  -> 'claro'
         *
         * 'rojo'   -> 'claro'
         *
         * Un array -> 'claro'
         */
        $tema = is_string($tema)
            && in_array($tema, ['claro', 'oscuro'], true)
                ? $tema
                : 'claro';


        /**
         * ====================================================
         * 12. RECIBIR EL NOMBRE DEL VISITANTE
         * ====================================================
         *
         * El formulario contiene un campo
         * llamado visitante.
         *
         * Este campo es opcional.
         *
         * El usuario puede introducir:
         *
         * Ana
         *
         * José
         *
         * María
         *
         * O dejarlo vacío.
         *
         * Recuperamos su valor desde $_POST.
         *
         * Si no se recibe el campo,
         * utilizamos una cadena vacía.
         */
        $nombre = $_POST['visitante'] ?? '';


        /**
         * ====================================================
         * 13. COMPROBAR EL TIPO Y ELIMINAR ESPACIOS
         * ====================================================
         *
         * Primero comprobamos que $nombre
         * sea una cadena mediante is_string().
         *
         * Si es una cadena, utilizamos trim().
         *
         * trim() elimina espacios en blanco
         * y determinados caracteres de control
         * al principio y al final de una cadena.
         *
         * EJEMPLO:
         *
         * "   Ana   "
         *
         * Después de trim():
         *
         * "Ana"
         *
         * Si el dato no es una cadena,
         * lo sustituimos por ''.
         *
         * Utilizamos un operador ternario
         * para expresar ambas posibilidades.
         */
        $nombre = is_string($nombre)
            ? trim($nombre)
            : '';


        /**
         * ====================================================
         * 14. ELIMINAR CARACTERES DE CONTROL
         * ====================================================
         *
         * Utilizamos preg_replace().
         *
         * Esta función permite buscar
         * patrones mediante expresiones regulares
         * y sustituir las coincidencias.
         *
         * Su estructura general es:
         *
         * preg_replace(
         *     patron,
         *     reemplazo,
         *     texto
         * )
         *
         * PRIMER ARGUMENTO:
         *
         * '/[\x00-\x1F\x7F]/u'
         *
         * Es una expresión regular.
         *
         * Los corchetes [] definen
         * un conjunto de caracteres.
         *
         * \x00-\x1F
         *
         * Representa el intervalo de códigos
         * comprendido entre hexadecimal 00 y 1F.
         *
         * Incluye caracteres de control
         * como tabulaciones y saltos de línea.
         *
         * \x7F
         *
         * Representa el carácter DEL.
         *
         * El modificador u indica
         * tratamiento UTF-8 del patrón.
         *
         * SEGUNDO ARGUMENTO:
         *
         * ''
         *
         * Sustituimos las coincidencias
         * por una cadena vacía.
         *
         * Es decir, eliminamos esos caracteres.
         *
         * TERCER ARGUMENTO:
         *
         * $nombre
         *
         * Es el texto que queremos procesar.
         *
         * EJEMPLO:
         *
         * Un nombre con un salto de línea
         * entre dos palabras perderá
         * ese salto de línea.
         *
         * OPERADOR ??
         *
         * preg_replace() puede devolver null
         * si se produce un error.
         *
         * Por eso utilizamos:
         *
         * ?? ''
         *
         * Si devuelve null,
         * guardamos una cadena vacía.
         *
         * IMPORTANTE:
         *
         * Esta operación elimina determinados
         * caracteres de control.
         *
         * No equivale a una validación completa
         * de todos los caracteres posibles.
         */
        $nombre = preg_replace(
            '/[\x00-\x1F\x7F]/u',
            '',
            $nombre
        ) ?? '';


        /**
         * ====================================================
         * 15. LIMITAR EL NOMBRE A 40 CARACTERES
         * ====================================================
         *
         * Utilizamos substr().
         *
         * Esta función permite extraer
         * una parte de una cadena de texto
         * teniendo en cuenta caracteres
         * multibyte, como los utilizados
         * en UTF-8.
         *
         * Su estructura es:
         *
         * substr(
         *     cadena,
         *     posicionInicial,
         *     longitud
         * )
         *
         * PRIMER ARGUMENTO:
         *
         * $nombre
         *
         * Es el texto que queremos recortar.
         *
         * SEGUNDO ARGUMENTO:
         *
         * 0
         *
         * Indica que empezamos
         * desde el primer carácter.
         *
         * TERCER ARGUMENTO:
         *
         * 40
         *
         * Indica que queremos conservar
         * como máximo 40 caracteres.
         *
         * EJEMPLO:
         *
         * Si el usuario escribe un nombre
         * de 50 caracteres, solamente
         * conservaremos los primeros 40.
         *
         * DIFERENCIA CON substr():
         *
         * substr() trabaja con posiciones
         * expresadas en bytes.
         *
         * substr() puede trabajar
         * correctamente con caracteres UTF-8.
         *
         * Esto es importante con nombres
         * que contienen letras como:
         *
         * á, é, í, ó, ú, ñ.
         *
         * IMPORTANTE:
         *
         * substr() requiere que esté
         * habilitada la extensión mbstring
         * en la instalación de PHP.
         *
         * Si no está disponible, se producirá
         * un error de función no definida.
         */
        $nombre = substr($nombre, 0, 40);


        /**
         * ====================================================
         * 16. GUARDAR LA COOKIE DEL TEMA
         * ====================================================
         *
         * Utilizamos setcookie() para enviar
         * al navegador la preferencia visual.
         *
         * PRIMER ARGUMENTO:
         *
         * 'tema'
         *
         * Es el nombre de la cookie.
         *
         * SEGUNDO ARGUMENTO:
         *
         * $tema
         *
         * Es el valor validado:
         *
         * claro u oscuro.
         *
         * TERCER ARGUMENTO:
         *
         * $opciones
         *
         * Contiene los atributos:
         *
         * - expires
         * - path
         * - secure
         * - httponly
         * - samesite
         *
         * EJEMPLO CONCEPTUAL:
         *
         * Set-Cookie: tema=oscuro; ...
         *
         * El navegador recibirá esa cabecera
         * y almacenará la cookie según
         * sus reglas y configuración.
         *
         * IMPORTANTE:
         *
         * setcookie() debe ejecutarse antes
         * de enviar contenido HTML.
         *
         * En este archivo estamos procesando
         * el formulario antes de llamar
         * a cabecera(), por lo que todavía
         * no hemos generado el HTML de la página.
         */
        setcookie('tema', $tema, $opciones);


        /**
         * ====================================================
         * 17. COMPROBAR SI EL NOMBRE ESTÁ VACÍO
         * ====================================================
         *
         * El nombre del visitante es opcional.
         *
         * Si contiene texto,
         * crearemos o actualizaremos su cookie.
         *
         * Si está vacío,
         * solicitaremos eliminar la cookie.
         *
         * Utilizamos !== para comprobar
         * que no sea una cadena vacía.
         *
         * El operador !== significa
         * estrictamente diferente.
         */
        if ($nombre !== '') {


            /**
             * ================================================
             * 18. GUARDAR EL NOMBRE DEL VISITANTE
             * ================================================
             *
             * Creamos o actualizamos
             * la cookie visitante.
             *
             * Nombre de la cookie:
             *
             * visitante
             *
             * Valor:
             *
             * El nombre validado y limitado
             * a 40 caracteres.
             *
             * Opciones:
             *
             * Las mismas utilizadas
             * para la cookie tema.
             *
             * EJEMPLO:
             *
             * visitante=Ana
             *
             * IMPORTANTE:
             *
             * El valor de esta cookie
             * se almacena en el navegador.
             *
             * No debemos considerar que
             * sea un dato confiable.
             *
             * En futuras peticiones,
             * volveremos a tratarlo como
             * información procedente del cliente.
             */
            setcookie('visitante', $nombre, $opciones);


        } else {


            /**
             * ================================================
             * 19. BORRAR EL NOMBRE SI ESTÁ VACÍO
             * ================================================
             *
             * Si el usuario deja vacío
             * el campo visitante,
             * queremos que desaparezca
             * cualquier cookie anterior
             * con ese nombre.
             *
             * Por ejemplo:
             *
             * Antes:
             *
             * visitante=Ana
             *
             * El usuario borra el texto
             * del formulario y pulsa Guardar.
             *
             * Entonces debemos eliminar
             * la cookie visitante anterior.
             *
             * Para ello modificamos
             * su fecha de caducidad.
             */
            $opciones['expires'] = time() - 3600;


            /**
             * Enviamos una cookie vacía
             * y con caducidad pasada.
             *
             * Esto solicita al navegador
             * que elimine la cookie visitante.
             *
             * La cookie tema no se elimina,
             * porque ya se ha enviado
             * anteriormente con su valor válido
             * y caducidad futura.
             */
            setcookie('visitante', '', $opciones);
        }
    }


    /**
     * ========================================================
     * 20. REDIRIGIR DESPUÉS DE PROCESAR EL FORMULARIO
     * ========================================================
     *
     * Una vez guardadas o eliminadas
     * las cookies, redirigimos al usuario
     * a preferencias.php.
     *
     * Utilizamos la función volver().
     *
     * Esta función realiza una redirección
     * HTTP 303 y termina la ejecución.
     *
     * El código 303 significa:
     *
     * See Other.
     *
     * Indica al navegador que realice
     * una nueva petición GET.
     *
     * Esto implementa el patrón:
     *
     * POST / Redirect / GET.
     *
     * ¿POR QUÉ ES IMPORTANTE AQUÍ?
     *
     * Porque $_COOKIE contiene los valores
     * recibidos al comenzar la petición.
     *
     * Cuando llamamos a setcookie(),
     * estamos enviando instrucciones
     * para que el navegador actualice
     * sus cookies.
     *
     * Pero $_COOKIE no se actualiza
     * automáticamente durante esa petición.
     *
     * Al redirigir:
     *
     * 1. El navegador recibe Set-Cookie.
     *
     * 2. Actualiza las cookies correspondientes.
     *
     * 3. Realiza una nueva petición GET.
     *
     * 4. Envía las cookies aplicables.
     *
     * 5. PHP puede recuperar sus nuevos valores.
     *
     * De esta forma el formulario mostrará
     * las preferencias actualizadas.
     */
    volver('preferencias.php');
}


/**
 * ============================================================
 * 21. RECUPERAR EL NOMBRE DESDE LA COOKIE
 * ============================================================
 *
 * Si llegamos a esta parte del archivo,
 * normalmente estamos mostrando la página
 * mediante una petición GET.
 *
 * $_COOKIE es una variable superglobal
 * que contiene las cookies recibidas
 * en la petición HTTP.
 *
 * En nuestro caso queremos recuperar
 * la cookie visitante.
 *
 * Por ejemplo:
 *
 * $_COOKIE['visitante']
 *
 * podría contener:
 *
 * 'Ana'
 *
 * Utilizamos ?? para evitar acceder
 * directamente a una clave inexistente.
 *
 * Si la cookie no existe,
 * utilizamos una cadena vacía.
 *
 * IMPORTANTE:
 *
 * Las cookies proceden del navegador.
 *
 * Sus valores pueden haber sido modificados
 * por el usuario.
 *
 * Por tanto, no debemos confiar
 * automáticamente en su contenido.
 */
$visitante = $_COOKIE['visitante'] ?? '';


/**
 * ============================================================
 * 22. COMPROBAR Y LIMITAR EL NOMBRE RECUPERADO
 * ============================================================
 *
 * Primero comprobamos si el valor
 * recuperado es una cadena.
 *
 * is_string($visitante)
 *
 * devuelve true si es de tipo string.
 *
 * Si es una cadena, aplicamos substr()
 * para limitarla a 40 caracteres.
 *
 * Si no es una cadena,
 * utilizamos una cadena vacía.
 *
 * ¿POR QUÉ VALIDAMOS OTRA VEZ?
 *
 * Porque las cookies pueden modificarse
 * desde el navegador.
 *
 * Aunque anteriormente hayamos guardado
 * un nombre válido, no podemos garantizar
 * que siga teniendo ese contenido.
 *
 * Esta comprobación también evita intentar
 * aplicar substr() sobre un array.
 *
 * Posteriormente utilizaremos e()
 * para escapar el texto al insertarlo
 * dentro de un atributo HTML.
 */
$visitante = is_string($visitante)
    ? substr($visitante, 0, 40)
    : '';


/**
 * ============================================================
 * 23. GENERAR LA CABECERA DE LA PÁGINA
 * ============================================================
 *
 * cabecera() es una función reutilizable
 * definida en includes/funciones.php.
 *
 * Recibe el título de la página:
 *
 * 'Preferencias'
 *
 * Genera la estructura HTML inicial
 * y la navegación del proyecto.
 *
 * También puede aplicar el tema visual
 * mediante la función temaActual().
 *
 * IMPORTANTE:
 *
 * Esta llamada se realiza después
 * de procesar las cookies.
 *
 * Así evitamos enviar HTML antes
 * de utilizar setcookie() o header().
 */
cabecera('Preferencias');

?>

<!--
    ============================================================
    24. SECCIÓN INTRODUCTORIA
    ============================================================

    <section> representa una sección temática
    dentro del documento HTML.

    La clase intro permite aplicar
    estilos específicos desde CSS.
-->
<section class="intro">

    <!--
        Texto descriptivo de la sección.

        Se utiliza una clase CSS llamada etiqueta
        para diferenciarlo visualmente.
    -->
    <p class="etiqueta">
        COOKIES EN EL CLIENTE
    </p>

    <!--
        Encabezado principal de la página.

        <h1> representa el título principal
        del contenido.
    -->
    <h1>
        Preferencias del visitante
    </h1>

    <!--
        Explicación del comportamiento
        de las cookies opcionales.

        Estas cookies solamente se crean
        cuando el usuario solicita guardarlas.

        La duración configurada es de 30 días
        desde el último guardado.
    -->
    <p>
        Estas cookies se crean únicamente cuando
        pulsas «Guardar preferencias» y duran
        hasta 30 días, salvo que las borres antes.
    </p>

</section>


<!--
    ============================================================
    25. CONTENEDOR DE LOS FORMULARIOS
    ============================================================

    <div> crea un contenedor genérico HTML.

    class="panel estrecho"

    Aplica dos clases CSS al mismo elemento.

    panel:
    Permite darle apariencia de panel.

    estrecho:
    Permite limitar su anchura.

    Las clases CSS no modifican
    la lógica de PHP.
-->
<div class="panel estrecho">


    <!--
        ========================================================
        26. FORMULARIO PARA GUARDAR PREFERENCIAS
        ========================================================

        <form> representa un formulario HTML.

        method="post"

        Indica que sus datos se enviarán
        mediante una petición HTTP POST.

        No indicamos action.

        Por tanto, el formulario se enviará
        a la URL actual, preferencias.php.

        Este archivo recibirá los datos
        y los procesará al comienzo.
    -->
    <form method="post">


        <!--
            ====================================================
            27. CAMPO OCULTO CON TOKEN CSRF
            ====================================================

            campoCsrf() es una función
            definida en funciones.php.

            Genera un input oculto
            con el token de seguridad.

            Por ejemplo:

            <input
                type="hidden"
                name="csrf_token"
                value="TOKEN"
            >

            El servidor validará este token
            mediante exigirPostValido().
        -->
        <?= campoCsrf() ?>


        <!--
            ====================================================
            28. INDICAR LA ACCIÓN GUARDAR
            ====================================================

            Este campo oculto indica
            qué operación queremos realizar.

            type="hidden"

            Significa que el campo
            no se muestra visualmente.

            name="accion"

            Es el nombre que recibirá PHP.

            value="guardar"

            Indica la operación solicitada.

            Al procesar el formulario:
            
            $_POST['accion']

            contendrá 'guardar'.

            IMPORTANTE:

            Los campos ocultos pueden
            modificarse desde DevTools.

            Por tanto, no deben considerarse
            un mecanismo de seguridad.
        -->
        <input
            type="hidden"
            name="accion"
            value="guardar"
        >


        <!--
            ====================================================
            29. ETIQUETA DEL CAMPO NOMBRE
            ====================================================

            <label> proporciona una descripción
            asociada a un campo del formulario.

            for="visitante"

            Relaciona esta etiqueta con
            el elemento cuyo id es visitante.

            Esta asociación mejora
            la accesibilidad y usabilidad.

            Al pulsar sobre la etiqueta,
            el navegador puede situar
            el foco en el campo asociado.
        -->
        <label for="visitante">
            Nombre (opcional)
        </label>


        <!--
            ====================================================
            30. CAMPO PARA INTRODUCIR EL NOMBRE
            ====================================================

            <input> crea un control de formulario.

            Como no indicamos type,
            el valor predeterminado es text.

            id="visitante"

            Identifica el elemento en HTML.

            name="visitante"

            Es el nombre utilizado al enviar
            el valor mediante POST.

            maxlength="40"

            Limita la longitud introducida
            mediante la interfaz del navegador.

            IMPORTANTE:

            Esta restricción no sustituye
            la limitación aplicada en PHP.

            value

            Muestra el nombre recuperado
            desde la cookie.

            La expresión:

            <?= e($visitante) ?>

            imprime el valor escapado
            para insertarlo de forma segura
            dentro del atributo HTML.

            e() utiliza htmlspecialchars().

            Esto evita que determinados caracteres
            del nombre se interpreten
            como parte del código HTML.

            placeholder="Ej.: Ana"

            Muestra un texto orientativo
            cuando el campo está vacío.

            El placeholder no sustituye
            la etiqueta label.
        -->
        <input
            id="visitante"
            name="visitante"
            maxlength="40"
            value="<?= e($visitante) ?>"
            placeholder="Ej.: Ana"
        >


        <!--
            ====================================================
            31. ETIQUETA DEL SELECTOR DE TEMA
            ====================================================

            Esta etiqueta describe
            el campo que permite elegir
            el tema visual de la página.

            for="tema"

            Se corresponde con id="tema"
            del elemento select.
        -->
        <label for="tema">
            Tema visual
        </label>


        <!--
            ====================================================
            32. LISTA DESPLEGABLE DE TEMAS
            ====================================================

            <select> crea una lista desplegable.

            id="tema"

            Identifica el elemento.

            name="tema"

            Indica el nombre del dato
            que se enviará mediante POST.

            Dentro de select utilizamos
            elementos option.

            Cada option representa
            una alternativa seleccionable.
        -->
        <select
            id="tema"
            name="tema"
        >


            <!--
                ================================================
                33. OPCIÓN TEMA CLARO
                ================================================

                value="claro"

                Es el valor enviado al servidor
                cuando se selecciona esta opción.

                El texto Claro es el contenido
                visible para el usuario.

                selected es un atributo booleano
                de HTML que marca una opción
                como seleccionada.

                Queremos añadir selected
                solamente cuando el tema
                actual sea claro.

                Para ello utilizamos:

                temaActual()

                Esta función consulta
                la preferencia de tema.

                OPERADOR TERNARIO:

                condicion ? verdadero : falso

                Si temaActual() devuelve 'claro',
                imprimimos 'selected'.

                Si devuelve 'oscuro',
                imprimimos una cadena vacía.
            -->
            <option
                value="claro"
                <?= temaActual() === 'claro'
                    ? 'selected'
                    : '' ?>
            >
                Claro
            </option>


            <!--
                ================================================
                34. OPCIÓN TEMA OSCURO
                ================================================

                Funciona igual que la opción anterior.

                value="oscuro"

                Es el valor enviado al servidor.

                Si temaActual() devuelve 'oscuro',
                añadimos el atributo selected.

                De esta manera, cuando el usuario
                vuelve a abrir preferencias.php,
                aparece seleccionada la opción
                que había guardado anteriormente.
            -->
            <option
                value="oscuro"
                <?= temaActual() === 'oscuro'
                    ? 'selected'
                    : '' ?>
            >
                Oscuro
            </option>

        </select>


        <!--
            ====================================================
            35. BOTÓN PARA GUARDAR PREFERENCIAS
            ====================================================

            type="submit"

            Indica que el botón envía
            el formulario al servidor.

            Cuando el usuario pulsa el botón,
            el navegador envía mediante POST:

            - csrf_token
            - accion = guardar
            - visitante
            - tema

            preferencias.php recibe estos datos,
            los valida y utiliza setcookie()
            para guardar las preferencias.
        -->
        <button type="submit">
            Guardar preferencias
        </button>

    </form>


    <!--
        ========================================================
        36. FORMULARIO PARA BORRAR COOKIES
        ========================================================

        Este formulario es independiente
        del formulario anterior.

        También utiliza POST.

        class="separado"

        Permite añadir separación visual
        mediante CSS.

        Su finalidad es solicitar
        la eliminación de las cookies
        tema y visitante.
    -->
    <form
        method="post"
        class="separado"
    >


        <!--
            Incluimos un token CSRF
            para proteger la operación.
        -->
        <?= campoCsrf() ?>


        <!--
            ====================================================
            37. INDICAR LA ACCIÓN BORRAR
            ====================================================

            Este campo oculto envía:

            accion = borrar

            Cuando preferencias.php
            procesa el formulario:

            $accion === 'borrar'

            será verdadero.

            Por tanto, se ejecutará
            el bloque que solicita
            eliminar ambas cookies.
        -->
        <input
            type="hidden"
            name="accion"
            value="borrar"
        >


        <!--
            ====================================================
            38. BOTÓN PARA BORRAR LAS PREFERENCIAS
            ====================================================

            Al pulsar este botón,
            el navegador envía el formulario.

            El servidor responde con cabeceras
            Set-Cookie que indican una fecha
            de caducidad pasada.

            class="secundario"

            Permite aplicar estilos CSS
            diferentes a este botón.
        -->
        <button
            type="submit"
            class="secundario"
        >
            Borrar cookies de preferencias
        </button>

    </form>


    <!--
        ========================================================
        39. NOTA DIDÁCTICA SOBRE LAS COOKIES
        ========================================================

        Este párrafo explica que existen
        diferentes tipos de cookies
        dentro de nuestra aplicación.

        COOKIE DE SESIÓN:

        Permite identificar la sesión PHP
        asociada al carrito.

        COOKIES DE PREFERENCIAS:

        Guardan el tema visual
        y el nombre opcional del visitante.

        Ambas cumplen funciones diferentes.

        Para observarlas podemos utilizar
        las herramientas de desarrollo
        del navegador.

        En Chrome o Edge:

        F12

        Application

        Cookies

        Desde ese panel podemos inspeccionar:

        - Nombre.
        - Valor.
        - Dominio.
        - Ruta.
        - Caducidad.
        - Secure.
        - HttpOnly.
        - SameSite.

        IMPORTANTE:

        Las cookies HttpOnly pueden inspeccionarse
        desde DevTools, aunque JavaScript
        no pueda leerlas mediante document.cookie.
    -->
    <p class="nota">
        La cookie de sesión, necesaria para el carrito,
        es independiente de estas preferencias.

        Para la práctica, inspecciona ambas en
        DevTools → Application/Storage → Cookies.
    </p>

</div>


<?php

/**
 * ============================================================
 * 40. MOSTRAR EL PIE DE PÁGINA
 * ============================================================
 *
 * pie() es una función reutilizable
 * definida en includes/funciones.php.
 *
 * Complementa a cabecera().
 *
 * cabecera() genera el inicio del documento.
 *
 * pie() genera el cierre.
 *
 * Normalmente incluye:
 *
 * - Cierre del contenido principal.
 * - Pie de página.
 * - Cierre de body.
 * - Cierre de html.
 *
 * De esta forma evitamos repetir
 * la misma estructura HTML
 * en todas las páginas del proyecto.
 */
pie();
