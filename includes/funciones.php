<?php
/** Funciones reutilizables: HTML, formato, validación y navegación. */
declare(strict_types=1);

// Evita interpretar como HTML los datos que se imprimen en la página.
function e(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Muestra un importe con formato habitual en España.
function euros(float $cantidad): string {
    return number_format($cantidad, 2, ',', '.') . ' €';
}

// Esta función se completará al aprender a guardar el carrito en una sesión.
function unidadesCarrito(): int {
    return 0;
}

// Esta función se completará cuando el carrito ya exista.
function totalCarrito(array $productos): float {
    return 0.0;
}

// Comprueba que el formulario se ha enviado por POST.
// La protección CSRF se añadirá en la última sesión.
function exigirPostValido(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Método no permitido.');
    }
}

function campoCsrf(): string
{
    $token = htmlspecialchars(
        "prueba",
        ENT_QUOTES,
        'UTF-8'
    );

    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// Patrón POST / REDIRECT / GET: evita repetir el envío al recargar.
function volver(string $pagina): never {
    header('Location: ' . $pagina, true, 303);
    exit;
}

// Al principio no existen preferencias persistentes.
function temaActual(): string {
    return 'claro';
}

function cabecera(string $titulo): void {
    // Con cookies recuperaremos aquí el nombre del visitante.
    $tema = temaActual();
    ?>
    <!doctype html>
    <html lang="es" data-tema="<?= e($tema) ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($titulo) ?> | MiniTienda PHP</title>
        <link rel="stylesheet" href="css/estilos.css">
    </head>
    <body>
    <header class="cabecera">
        <div class="contenedor barra">
            <a class="marca" href="index.php">MiniTienda PHP</a>
            <nav aria-label="Navegación principal">
                <a href="index.php">Catálogo</a>
                <a href="carrito.php">Carrito (<?= unidadesCarrito() ?>)</a>
                <a href="preferencias.php">Preferencias</a>
                <a href="sesion.php">Sesión</a>
            </nav>
        </div>
    </header>
    <main class="contenedor">
    <?php
}

function pie(): void {
    ?>
    </main>
    <footer class="pie">Proyecto didáctico de DWES · Unidad 4 · Docente: José Manuel Villar Ferradal</footer>
    </body></html>
    <?php
}
