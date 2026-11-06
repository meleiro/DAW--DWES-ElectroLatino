
<?php

/*
============================================================
1. CARGAR LA CONFIGURACIÓN GENERAL
============================================================

require_once permite incluir y ejecutar otro archivo PHP.

En nuestro proyecto, config.php se encarga de preparar
elementos comunes, como:

- La sesión PHP.
- Las funciones auxiliares del proyecto.
- El token de seguridad CSRF.
- La variable $https.

__DIR__ es una constante especial de PHP que contiene
la ruta absoluta del directorio del archivo actual.

Por ejemplo, si preferencias.php está en:

C:\proyecto\ELECTROLATINO\preferencias.php

__DIR__ representa:

C:\proyecto\ELECTROLATINO

Al concatenar '/includes/config.php' construimos
la ruta del archivo que queremos cargar.

Utilizamos require_once en lugar de require para
evitar que el mismo archivo se cargue varias veces.
*/

require_once __DIR__ . '/includes/config.php';


/*
============================================================
2. COMPROBAR SI SE HA ENVIADO UN FORMULARIO
============================================================

$_SERVER es un array superglobal de PHP.

Contiene información sobre el servidor y sobre
la petición HTTP que está procesando.

La clave REQUEST_METHOD indica el método HTTP
utilizado por el navegador.

Algunos métodos habituales son:

GET:
Se utiliza normalmente para solicitar una página
o consultar información.

POST:
Se utiliza habitualmente para enviar datos que
van a procesarse en el servidor.

En nuestro caso:

- Al abrir preferencias.php desde un enlace,
  normalmente recibimos una petición GET.

- Al pulsar uno de los botones de los formularios,
  recibimos una petición POST.

El operador ?? se denomina operador de
fusión de null (null coalescing).

La expresión:

$_SERVER['REQUEST_METHOD'] ?? ''

significa:

"Obtén REQUEST_METHOD si existe y no es null.
En caso contrario, utiliza una cadena vacía".

Utilizamos === para realizar una comparación estricta.

Por tanto, este bloque solamente se ejecuta
cuando la petición utiliza el método POST.
*/

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {


    /*
    ========================================================
    3. COMPROBAR LA SEGURIDAD DEL FORMULARIO
    ========================================================

    exigirPostValido() es una función auxiliar
    definida en nuestro proyecto.

    Su finalidad es comprobar que la petición
    cumple las condiciones de seguridad previstas.

    En particular:

    1. Comprueba que la petición utiliza POST.

    2. Recupera el token CSRF enviado por el formulario.

    3. Compara ese token con el almacenado en la sesión.

    CSRF significa Cross-Site Request Forgery.

    Este tipo de ataque intenta conseguir que el
    navegador de un usuario envíe una petición
    no deseada a una aplicación.

    Para reducir este riesgo, cada formulario incluye
    un token que debe coincidir con el de la sesión.

    Si la comprobación falla, nuestra función
    interrumpe la ejecución de la petición.
    */

    exigirPostValido();


    /*
    ========================================================
    4. IDENTIFICAR QUÉ FORMULARIO SE HA ENVIADO
    ========================================================

    En esta página tenemos dos formularios diferentes:

    FORMULARIO 1:
    Guardar las preferencias.

    FORMULARIO 2:
    Borrar las cookies de preferencias.

    Ambos utilizan POST y envían sus datos
    a preferencias.php.

    ¿Cómo distinguimos qué botón ha pulsado el usuario?

    Mediante un campo oculto llamado "accion".

    En el primer formulario:

    <input type="hidden" name="accion" value="guardar">

    En el segundo:

    <input type="hidden" name="accion" value="borrar">

    Cuando el navegador envía el formulario,
    PHP recibe el valor mediante $_POST.

    $_POST es un array asociativo superglobal
    que contiene los datos de los formularios
    enviados mediante POST, cuando utilizan
    una codificación compatible.

    Por ejemplo:

    $_POST['accion'] = 'guardar';

    o:

    $_POST['accion'] = 'borrar';

    Si no se recibe el campo accion,
    utilizamos "guardar" como valor predeterminado.
    */

    $accion = $_POST['accion'] ?? 'guardar';


    /*
    ========================================================
    5. PREPARAR LAS OPCIONES DE LAS COOKIES
    ========================================================

    Una cookie es un pequeño dato que el servidor
    puede solicitar al navegador que almacene.

    En posteriores peticiones al mismo sitio,
    el navegador puede enviar esa cookie al servidor.

    En este proyecto utilizaremos dos cookies:

    tema:
    Guardará la preferencia visual del visitante.

    Posibles valores:
    - claro
    - oscuro

    visitante:
    Guardará el nombre que escriba el usuario.

    Para configurar las cookies utilizamos
    un array asociativo llamado $opciones.

    Un array asociativo almacena pares clave-valor.

    Por ejemplo:

    'path' => '/'

    La clave es "path" y su valor es "/".

    PHP permite pasar este array como tercer
    argumento de setcookie().
    */

    $opciones = [


        /*
        ----------------------------------------------------
        expires: fecha de caducidad
        ----------------------------------------------------

        time() devuelve un timestamp Unix.

        Es decir, el número de segundos transcurridos
        desde el 1 de enero de 1970 a las 00:00:00 UTC.

        Queremos que la cookie caduque dentro de 30 días.

        Calculamos:

        30 días
        x 24 horas
        x 60 minutos
        x 60 segundos

        Resultado:

        2.592.000 segundos.

        Sumamos esa cantidad al instante actual.

        Así obtenemos una fecha de caducidad situada
        aproximadamente 30 días en el futuro.

        Si el navegador acepta la cookie, podrá
        conservarla hasta esa fecha, salvo que
        se borre antes.
        */

        'expires' => time() + 30 * 24 * 60 * 60,


        /*
        ----------------------------------------------------
        path: ruta donde se enviará la cookie
        ----------------------------------------------------

        El valor '/' indica que la cookie puede
        enviarse en las peticiones a cualquier ruta
        del sitio correspondiente.

        Por ejemplo:

        /index.php
        /carrito.php
        /preferencias.php

        Si utilizáramos una ruta más específica,
        el navegador limitaría el envío de la cookie
        a esa ruta y sus subrutas correspondientes.

        IMPORTANTE:

        La ruta no hace que la cookie se comparta
        entre dominios diferentes.
        */

        'path' => '/',


        /*
        ----------------------------------------------------
        secure: envío mediante conexiones seguras
        ----------------------------------------------------

        La opción secure indica si la cookie
        debe restringirse a conexiones seguras.

        En una web publicada con HTTPS,
        normalmente utilizaremos true.

        En nuestro proyecto, $https es una variable
        definida en la configuración general.

        Su valor depende de si la petición actual
        utiliza HTTPS.

        Por ejemplo:

        https://mitienda.es

        Utilizaría normalmente secure = true.

        En desarrollo local con:

        http://localhost:8000

        normalmente utilizaremos false.

        De esta manera podemos probar las cookies
        con el servidor integrado de PHP.

        En producción debemos utilizar HTTPS.
        */

        'secure' => $https,


        /*
        ----------------------------------------------------
        httponly: impedir la lectura desde JavaScript
        ----------------------------------------------------

        Si establecemos:

        'httponly' => true

        el navegador impide que JavaScript acceda
        a la cookie mediante document.cookie.

        Por ejemplo, esta instrucción:

        console.log(document.cookie);

        no mostrará las cookies marcadas como HttpOnly.

        PHP sí podrá recibirlas mediante $_COOKIE
        cuando el navegador las envíe.

        Esto ayuda a proteger los valores frente
        a determinados ataques de JavaScript.

        Sin embargo, no elimina por completo
        el riesgo de ataques XSS.

        En nuestro proyecto no necesitamos que
        JavaScript lea estas preferencias porque
        será PHP quien determine el tema visual.
        */

        'httponly' => true,


        /*
        ----------------------------------------------------
        samesite: restringir determinados envíos
        entre sitios
        ----------------------------------------------------

        SameSite controla en qué situaciones
        el navegador envía una cookie cuando
        una petición se origina desde otro sitio.

        Los valores habituales son:

        Strict:
        Es la opción más restrictiva.

        Lax:
        Permite algunos usos entre sitios, como
        determinadas navegaciones principales
        mediante enlaces, pero restringe otros.

        None:
        Permite el uso entre sitios cuando se
        cumplen los requisitos del navegador,
        incluido Secure.

        Utilizamos Lax porque proporciona
        una protección razonable para este ejemplo.

        No sustituye a la validación del token CSRF.
        */

        'samesite' => 'Lax',

    ];


    /*
    ========================================================
    6. COMPROBAR SI EL USUARIO QUIERE BORRAR LAS COOKIES
    ========================================================

    Si el usuario pulsa el botón:

    "Borrar cookies de preferencias"

    El formulario envía:

    accion=borrar

    Entonces se cumple:

    $accion === 'borrar'

    Y ejecutamos el siguiente bloque.
    */

    if ($accion === 'borrar') {


        /*
        ----------------------------------------------------
        Modificar la fecha de caducidad
        ----------------------------------------------------

        Para solicitar al navegador que elimine
        una cookie, podemos enviar otra cookie
        con el mismo nombre y ámbito, pero
        con una fecha de caducidad pasada.

        time() devuelve el instante actual.

        time() - 3600 representa una hora antes.

        Por tanto, indicamos al navegador que
        la cookie ya ha caducado.

        Estamos modificando la clave expires
        del array asociativo $opciones.

        El resto de las opciones permanece igual.
        */

        $opciones['expires'] = time() - 3600;


        /*
        ----------------------------------------------------
        Borrar la cookie del tema
        ----------------------------------------------------

        setcookie() permite enviar al navegador
        una cabecera HTTP Set-Cookie.

        En este caso:

        Primer argumento:
        'tema' -> nombre de la cookie.

        Segundo argumento:
        '' -> valor vacío.

        Tercer argumento:
        $opciones -> configuración de la cookie.

        Como expires contiene una fecha pasada,
        el navegador debe eliminar la cookie
        correspondiente si coinciden su nombre,
        dominio y ruta.

        El borrado no elimina inmediatamente
        el valor de $_COOKIE en esta petición.
        */

        setcookie('tema', '', $opciones);


        /*
        ----------------------------------------------------
        Borrar la cookie del visitante
        ----------------------------------------------------

        Repetimos la operación con la cookie
        llamada "visitante".

        Así eliminamos las dos preferencias
        que queremos gestionar desde esta página.
        */

        setcookie('visitante', '', $opciones);


    } else {


        /*
        ====================================================
        7. PROCESAR LA ACCIÓN DE GUARDAR PREFERENCIAS
        ====================================================

        Este bloque se ejecuta cuando $accion
        es diferente de "borrar".

        Por ejemplo, cuando el usuario pulsa:

        "Guardar preferencias".

        De momento vamos a recuperar y validar
        el tema seleccionado.

        TODAVÍA NO ESTAMOS GUARDANDO LA COOKIE
        EN ESTE BLOQUE.
        */


        /*
        ----------------------------------------------------
        Recuperar el tema enviado por el formulario
        ----------------------------------------------------

        El formulario contiene:

        <select id="tema" name="tema">

        Y sus opciones son:

        <option value="claro">Claro</option>
        <option value="oscuro">Oscuro</option>

        Cuando el usuario selecciona una opción,
        el navegador envía el valor correspondiente.

        Por ejemplo:

        tema=oscuro

        Recuperamos el dato con:

        $_POST['tema']

        Si no existe, utilizamos "claro".
        */

        $tema = $_POST['tema'] ?? 'claro';


        /*
        ----------------------------------------------------
        Validar el tema recibido
        ----------------------------------------------------

        Aunque el formulario HTML solamente permite
        seleccionar "claro" u "oscuro", no debemos
        confiar ciegamente en los datos recibidos.

        Un usuario podría modificar la petición
        utilizando las herramientas del navegador
        u otra aplicación.

        Por eso comprobamos dos condiciones.

        PRIMERA CONDICIÓN:

        is_string($tema)

        Comprueba que el valor recibido sea
        una cadena de texto.

        Esto es importante porque alguien podría
        enviar un array en lugar de una cadena.

        SEGUNDA CONDICIÓN:

        in_array($tema, ['claro', 'oscuro'], true)

        Comprueba que el tema esté dentro
        de la lista de valores permitidos.

        El tercer argumento, true, activa
        la comparación estricta.

        Ambas condiciones se combinan con &&.

        El operador && significa AND lógico:
        las dos condiciones deben ser verdaderas.

        Finalmente utilizamos el operador ternario:

        condición ? valor_si_true : valor_si_false

        Si ambas comprobaciones son correctas,
        conservamos el tema recibido.

        En caso contrario, utilizamos "claro".

        EJEMPLOS:

        "oscuro" -> "oscuro"
        "claro"  -> "claro"
        "rojo"   -> "claro"
        array    -> "claro"
        */

/*
============================================================
1. VALIDAR EL TEMA SELECCIONADO POR EL USUARIO
============================================================

La variable $tema contiene el valor que hemos recuperado
previamente del formulario mediante $_POST.

Queremos asegurarnos de que solamente pueda contener
uno de estos dos valores:

- 'claro'
- 'oscuro'

Para ello utilizamos dos funciones y un operador ternario.
*/

// is_string($tema):
// Comprueba que el valor recibido sea una cadena de texto.
//
// Devuelve:
// true  -> si es una cadena.
// false -> si es otro tipo de dato, por ejemplo, un array.
//
// in_array($tema, ['claro', 'oscuro'], true):
// Comprueba si el valor de $tema se encuentra dentro
// del array de valores permitidos.
//
// El tercer argumento, true, obliga a realizar
// una comparación estricta, teniendo en cuenta
// tanto el valor como su tipo.
//
// El operador && significa AND lógico:
// ambas condiciones deben cumplirse.
//
// El operador ternario tiene esta estructura:
//
// condicion ? valor_si_verdadero : valor_si_falso
//
// Si el tema es válido, conservamos su valor.
// Si no lo es, utilizamos 'claro' como valor por defecto.

$tema = is_string($tema) &&
        in_array($tema, ['claro', 'oscuro'], true)
        ? $tema : 'claro';


/*
============================================================
2. GUARDAR LA COOKIE DEL TEMA
============================================================

setcookie() envía una cabecera HTTP al navegador
para solicitar que almacene una cookie.

Recibe tres argumentos:

1. 'tema':
   Nombre de la cookie.

2. $tema:
   Valor que queremos almacenar ('claro' u 'oscuro').

3. $opciones:
   Array asociativo que hemos definido previamente
   con las opciones de configuración.

   Por ejemplo:
   - expires: fecha de caducidad.
   - path: ruta donde se enviará la cookie.
   - secure: restricción a conexiones seguras.
   - httponly: impide leerla desde document.cookie.
   - samesite: controla determinados envíos entre sitios.

IMPORTANTE:

setcookie() no modifica inmediatamente $_COOKIE.

El nuevo valor estará disponible en una petición posterior,
cuando el navegador envíe la cookie al servidor.
*/

setcookie('tema', $tema, $opciones);


/*
============================================================
3. RECUPERAR EL NOMBRE DEL VISITANTE
============================================================

$_POST es un array superglobal que contiene los datos
enviados mediante un formulario con método POST.

El campo HTML correspondiente es:

<input name="visitante" ...>

Por tanto, recuperamos su valor mediante:

$_POST['visitante']

El operador ?? significa:

"Si el valor existe y no es null, utilízalo.
En caso contrario, utiliza el valor alternativo".

Si no recibimos el nombre, utilizamos una cadena vacía.
*/

$nombre = $_POST['visitante'] ?? '';


/*
============================================================
4. COMPROBAR QUE EL NOMBRE SEA TEXTO
============================================================

Aunque el formulario contiene un campo de texto,
no debemos confiar completamente en los datos recibidos.

Un usuario podría manipular la petición y enviar
un array en lugar de una cadena.

Por ejemplo:

visitante[]=Ana

En ese caso, $_POST['visitante'] podría ser un array.

Utilizamos is_string() para comprobar el tipo.

Si es una cadena:
    Aplicamos trim($nombre).

Si no es una cadena:
    Asignamos una cadena vacía.

trim() elimina los espacios en blanco y otros caracteres
de separación habituales al principio y al final.

Ejemplo:

"   Ana López   " -> "Ana López"
*/

$nombre = is_string($nombre) ? trim($nombre) : '';


/*
============================================================
5. ELIMINAR CARACTERES DE CONTROL
============================================================

preg_replace() permite buscar y sustituir partes
de una cadena utilizando expresiones regulares.

Su estructura es:

preg_replace(patron, sustitucion, texto);

En nuestro caso:

PATRÓN:
'/[\x00-\x1F\x7F]/u'

SUSTITUCIÓN:
''

TEXTO:
$nombre

Vamos a analizar la expresión regular:

[ ... ]
Define una clase de caracteres: cualquiera de los
caracteres incluidos en ella.

\x00-\x1F
Representa los caracteres cuyos códigos hexadecimales
están comprendidos entre 00 y 1F.

Incluye caracteres de control como:
- Tabuladores.
- Saltos de línea.
- Retornos de carro.

\x7F
Representa el carácter de control DEL.

u
Indica que la expresión regular trabaja en modo UTF-8.

La cadena vacía '' indica que queremos eliminar
los caracteres encontrados.

El operador ?? '' establece una cadena vacía si
preg_replace() devuelve null.

Ejemplo:

"Ana\nLópez" -> "AnaLópez"

NOTA:
Si preg_replace() encuentra UTF-8 no válido, puede
devolver null y el nombre quedará vacío.
*/

$nombre = preg_replace(
    '/[\x00-\x1F\x7F]/u',
    '',
    $nombre
) ?? '';


/*
============================================================
6. LIMITAR LA LONGITUD DEL NOMBRE
============================================================

substr() permite extraer una parte de una cadena.

Su estructura es:

substr(cadena, posicion_inicial, longitud);

En nuestro caso:

$nombre:
Cadena sobre la que queremos trabajar.

0:
Comenzamos desde la primera posición.

40:
Extraemos como máximo 40 bytes.

Ejemplo con caracteres ASCII:

"Antonio" -> "Antonio"

Si el nombre supera los 40 bytes, se recorta.

IMPORTANTE:

substr() trabaja con bytes, no con caracteres Unicode.

Por tanto, en nombres con tildes, eñes u otros
caracteres UTF-8, podría cortar un carácter a la mitad.

Para limitar a 40 caracteres Unicode es preferible:

mb_substr($nombre, 0, 40, 'UTF-8');

Esta alternativa requiere la extensión mbstring.
*/

$nombre = substr($nombre, 0, 40);


/*
============================================================
7. COMPROBAR SI EL VISITANTE HA INTRODUCIDO UN NOMBRE
============================================================

El operador !== realiza una comparación estricta
de desigualdad.

En este caso:

$nombre !== ''

Significa:

"El nombre no es una cadena vacía".

Si contiene algún carácter, guardamos la cookie.

Si está vacío, eliminamos una posible cookie anterior.
*/

if ($nombre !== '') {

    /*
    --------------------------------------------------------
    8. GUARDAR LA COOKIE DEL VISITANTE
    --------------------------------------------------------

    Enviamos al navegador una cookie llamada 'visitante'.

    Su contenido será el nombre validado.

    Utilizamos el mismo array $opciones que para
    la cookie del tema.

    Por tanto, tendrá la misma configuración
    de duración, ruta y seguridad.
    */

    setcookie('visitante', $nombre, $opciones);

} else {

    /*
    --------------------------------------------------------
    9. BORRAR LA COOKIE SI EL NOMBRE ESTÁ VACÍO
    --------------------------------------------------------

    Imaginemos que el usuario había guardado
    anteriormente el nombre "Ana".

    Después vuelve al formulario, borra el nombre
    y pulsa "Guardar preferencias".

    No queremos conservar el nombre anterior.

    Para solicitar la eliminación de una cookie,
    enviamos otra con:

    - El mismo nombre.
    - La misma ruta y dominio.
    - Una fecha de caducidad pasada.

    time() devuelve el instante actual como
    número de segundos desde la época Unix.

    Restamos 3600 segundos (una hora).

    De este modo, la cookie ya estará caducada.

    Estamos modificando la clave 'expires'
    del array asociativo $opciones.
    */

    $opciones['expires'] = time() - 3600;

    /*
    Enviamos la cookie 'visitante' con valor vacío
    y una fecha de caducidad pasada.

    Esto solicita al navegador que la elimine.

    En el código original utilizabas $nombre
    como segundo argumento.

    Como en este bloque $nombre es '', ambas
    instrucciones son equivalentes.

    Utilizamos '' directamente para dejar más
    clara nuestra intención de borrar la cookie.
    */

    setcookie('visitante', '', $opciones);
}









        /*
        ====================================================
        PENDIENTE PARA LA SIGUIENTE PARTE DE LA PRÁCTICA
        ====================================================

        En este punto ya tenemos el tema validado.

        Sin embargo, todavía no llamamos a setcookie()
        para guardar el valor.

        También queda pendiente:

        - Recuperar el nombre del visitante.
        - Validar y limitar su longitud.
        - Guardar la cookie visitante.
        - Redirigir después del POST.

        Por tanto, actualmente el botón Guardar
        procesa el formulario, pero no almacena
        las preferencias.
        */

    }
    volver('preferencias.php');

}


/*
============================================================
8. GENERAR LA CABECERA HTML
============================================================

cabecera() es una función auxiliar del proyecto.

Su finalidad es generar la estructura común
de las páginas de MiniTienda.

Por ejemplo:

- <!DOCTYPE html>
- <html>
- <head>
- El título de la página.
- Los enlaces a las hojas de estilos.
- El encabezado y el menú de navegación.

Le pasamos la cadena "Preferencias" para
identificar el título de la página.

Es importante que cualquier llamada a setcookie()
se realice antes de generar la salida HTML,
porque las cookies se envían mediante cabeceras HTTP.

Por eso hemos colocado el procesamiento POST
antes de llamar a cabecera().
*/

cabecera('Preferencias');


/*
============================================================
9. INICIALIZAR LA VARIABLE DEL VISITANTE
============================================================

Creamos una variable llamada $visitante.

De momento le asignamos una cadena vacía.

Esto evita trabajar con una variable no definida
cuando posteriormente queramos mostrar su valor
en el formulario.

En esta versión todavía no estamos recuperando
la cookie del visitante.

Por tanto, siempre tendrá el valor ''.
*/

$visitante = $_COOKIE['visitante'] ?? '';
$visitante = is_string($visitante) ? substr($visitante,0,40) : '';

?>

<!--
============================================================
10. SECCIÓN DE PRESENTACIÓN
============================================================

A partir de aquí mezclamos HTML y PHP.

PHP ya ha ejecutado la lógica del servidor
y ahora generamos el contenido que recibirá
el navegador.

La etiqueta <section> representa una sección
temática del documento HTML.

La clase "intro" permite aplicar estilos
desde nuestra hoja CSS.

El encabezado <h1> representa el título
principal de esta página.

Las etiquetas <p> representan párrafos.
-->

<section class="intro">

    <p class="etiqueta">COOKIES EN EL CLIENTE</p>

    <h1>Preferencias del visitante</h1>

    <p>
        En esta página podrás configurar tus preferencias
        de navegación, como tu nombre y el tema visual.
    </p>

</section>


<!--
============================================================
11. CONTENEDOR PRINCIPAL
============================================================

La etiqueta <div> es un contenedor genérico.

En este caso utilizamos dos clases CSS:

panel:
Permite aplicar el aspecto visual de un panel.

estrecho:
Puede utilizarse para limitar su anchura.

Estas clases son propias de nuestro proyecto.
-->

<div class="panel estrecho">


    <!--
    ========================================================
    12. FORMULARIO PARA GUARDAR PREFERENCIAS
    ========================================================

    La etiqueta <form> permite enviar información
    introducida por el usuario.

    method="post" indica que los datos se enviarán
    utilizando el método HTTP POST.

    No hemos definido el atributo action.

    Cuando action se omite, el formulario se envía
    a la URL del documento actual.

    Por tanto, desde preferencias.php,
    el navegador enviará los datos a esa misma página.

    PHP procesará el formulario mediante el bloque
    POST que hemos escrito al principio del archivo.
    -->

    <form method="post">


        <?php
        /*
        ====================================================
        13. INSERTAR EL TOKEN CSRF
        ====================================================

        campoCsrf() es una función auxiliar
        que hemos definido en el proyecto.

        Devuelve el HTML de un campo oculto
        con el token de seguridad.

        Por ejemplo:

        <input
            type="hidden"
            name="csrf_token"
            value="TOKEN_GENERADO"
        >

        La sintaxis:

        <?= expresion ?>

        es una forma abreviada de:

        <?php echo expresion; ?>

        Se utiliza para imprimir un valor
        dentro del documento HTML.

        Cuando se envía el formulario, el token
        llegará al servidor junto con los demás datos.

        exigirPostValido() comprobará su validez.
        */
        ?>

        <?= campoCsrf() ?>


        <!--
        ====================================================
        14. CAMPO OCULTO PARA IDENTIFICAR LA ACCIÓN
        ====================================================

        type="hidden" crea un campo que no se muestra
        visualmente en el formulario.

        Sin embargo, su valor se envía al servidor.

        name="accion" determina el nombre de la clave
        que recibiremos en $_POST.

        value="guardar" indica qué operación
        queremos realizar.

        Por tanto, al enviar este formulario:

        $_POST['accion'] contendrá "guardar".

        IMPORTANTE:

        Los campos hidden no son mecanismos
        de seguridad, porque pueden modificarse
        desde las herramientas del navegador.
        -->

        <input
            type="hidden"
            name="accion"
            value="guardar"
        >


        <!--
        ====================================================
        15. ETIQUETA DEL CAMPO NOMBRE
        ====================================================

        <label> permite asociar un texto descriptivo
        a un control del formulario.

        El atributo for debe coincidir con el id
        del elemento correspondiente.

        En este caso:

        for="visitante"

        está asociado con:

        id="visitante"

        Esto mejora la accesibilidad.

        Además, al pulsar sobre la etiqueta,
        el navegador puede situar el foco
        en el campo de texto asociado.
        -->

        <label for="visitante">
            Nombre (opcional)
        </label>


        <!--
        ====================================================
        16. CAMPO DE TEXTO DEL VISITANTE
        ====================================================

        type="text":
        Permite introducir texto.

        id="visitante":
        Identifica el elemento dentro del documento.

        name="visitante":
        Es el nombre que utilizará PHP para
        recuperar el valor mediante $_POST.

        Por ejemplo:

        $_POST['visitante']

        maxlength="40":
        Limita a 40 caracteres la entrada habitual
        realizada mediante el control HTML.

        Esta restricción del navegador no sustituye
        a la validación que debemos hacer en PHP.

        value="":
        Indica que inicialmente el campo estará vacío.

        placeholder="Ej.: Ana":
        Muestra un texto orientativo cuando
        el campo está vacío.

        El placeholder no sustituye al label.

        En una fase posterior podremos recuperar
        el nombre desde una cookie y mostrarlo
        como valor inicial del campo.
        -->

        <input
            type="text"
            id="visitante"
            name="visitante"
            maxlength="40"
            value="<?= e($visitante) ?>"
            placeholder="Ej.: Ana"
        >


        <!--
        ====================================================
        17. ETIQUETA DEL SELECTOR DE TEMA
        ====================================================

        Esta etiqueta identifica el desplegable
        donde el usuario seleccionará el tema.

        El atributo for="tema" está relacionado
        con el elemento que tiene id="tema".
        -->

        <label for="tema">
            Tema visual
        </label>


        <!--
        ====================================================
        18. SELECTOR DESPLEGABLE
        ====================================================

        <select> crea una lista desplegable.

        id="tema":
        Identificador del elemento HTML.

        name="tema":
        Nombre utilizado para enviar el dato.

        Cuando el usuario envía el formulario,
        PHP puede acceder al valor mediante:

        $_POST['tema']

        Dentro del select utilizamos elementos
        <option> para definir las opciones.

        Cada option tiene:

        value:
        El dato que se enviará al servidor.

        Texto visible:
        Lo que verá el usuario en pantalla.
        -->

        <select id="tema" name="tema">


            <!--
            Opción del tema claro.

            value="claro" es el dato que se enviará.

            selected indica que esta opción
            aparece seleccionada inicialmente.

            En esta versión siempre se selecciona
            Claro al cargar la página.
            -->

            <option value="claro" selected>
                Claro
            </option>


            <!--
            Opción del tema oscuro.

            Si el usuario la selecciona,
            el navegador enviará:

            tema=oscuro
            -->

            <option value="oscuro">
                Oscuro
            </option>

        </select>


        <!--
        ====================================================
        19. BOTÓN PARA ENVIAR EL FORMULARIO
        ====================================================

        type="submit" indica que el botón
        debe enviar el formulario al servidor.

        Cuando el usuario pulsa este botón:

        1. El navegador recoge los campos del formulario.

        2. Envía una petición POST a preferencias.php.

        3. PHP comprueba el token CSRF.

        4. Recupera la acción "guardar".

        5. Recupera y valida el tema.

        En esta versión todavía no guarda
        las cookies de las preferencias.
        -->

        <button type="submit">
            Guardar preferencias
        </button>

    </form>


    <!--
    ========================================================
    20. SEGUNDO FORMULARIO: BORRAR COOKIES
    ========================================================

    Creamos otro formulario independiente.

    También utiliza el método POST.

    La clase "separado" permite aplicar
    estilos CSS para distinguirlo o separarlo
    visualmente del formulario anterior.

    Este segundo formulario enviará:

    accion=borrar

    Así el servidor podrá identificar
    la operación solicitada.
    -->

    <form method="post" class="separado">


        <?php
        /*
        Este formulario también necesita
        su propio token CSRF.

        Los dos formularios pueden compartir
        el token asociado a la misma sesión,
        pero cada uno debe incluir el campo
        correspondiente para enviarlo.
        */
        ?>

        <?= campoCsrf() ?>


        <!--
        Campo oculto que indica al servidor
        que debe ejecutar la acción de borrado.
        -->

        <input
            type="hidden"
            name="accion"
            value="borrar"
        >


        <!--
        ====================================================
        21. BOTÓN PARA BORRAR LAS COOKIES
        ====================================================

        type="submit":
        Envía el formulario.

        class="secundario":
        Permite utilizar un estilo visual diferente
        al botón principal.

        Al pulsarlo:

        1. Se envía una petición POST.

        2. PHP recibe accion=borrar.

        3. Se ejecuta el bloque:

           if ($accion === 'borrar')

        4. Se establece una fecha de caducidad pasada.

        5. Se envían instrucciones al navegador
           para eliminar las cookies tema y visitante.

        La eliminación efectiva depende de que
        el navegador procese esas instrucciones.
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
    22. NOTA INFORMATIVA
    ========================================================

    Este párrafo explica al visitante que
    las preferencias se almacenarán mediante cookies.

    La clase "nota" permite darle un estilo
    visual diferenciado.

    En esta fase, el borrado ya está programado,
    pero el guardado sigue pendiente.
    -->

    <p class="nota">
        Las preferencias del visitante se almacenarán
        mediante cookies del navegador.
    </p>

</div>


<?php

/*
============================================================
23. GENERAR EL PIE DE PÁGINA
============================================================

pie() es otra función auxiliar de nuestro proyecto.

Normalmente se encarga de generar:

- El pie de página visual.
- Las etiquetas de cierre de los contenedores comunes.
- Las etiquetas </body> y </html>.

Utilizar funciones comunes como cabecera() y pie()
permite mantener una estructura uniforme en
todas las páginas de MiniTienda.

Además, si queremos modificar el encabezado
o el pie, solamente tendremos que cambiar
la función correspondiente.
*/

pie();
