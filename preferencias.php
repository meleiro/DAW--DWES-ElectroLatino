<?php
require_once __DIR__ . '/includes/config.php';
cabecera('Preferencias');
?>
<section class="intro">
    <p class="etiqueta">COOKIES EN EL CLIENTE</p>
    <h1>Preferencias del visitante</h1>
    <p>Estas cookies se crean únicamente cuando pulsas «Guardar preferencias» y duran hasta 30 días, salvo que las borres antes.</p>
</section>
<div class="panel estrecho">
    <form method="post">
        <?= campoCsrf() ?>
        <input type="hidden" name="accion" value="guardar">
        <label for="visitante">Nombre (opcional)</label>
        <input id="visitante" name="visitante" maxlength="40" value="<?= e($visitante) ?>" placeholder="Ej.: Ana">
        <label for="tema">Tema visual</label>
        <select id="tema" name="tema">
            <option value="claro" <?= temaActual() === 'claro' ? 'selected' : '' ?>>Claro</option>
            <option value="oscuro" <?= temaActual() === 'oscuro' ? 'selected' : '' ?>>Oscuro</option>
        </select>
        <button type="submit">Guardar preferencias</button>
    </form>
    <form method="post" class="separado">
        <?= campoCsrf() ?>
        <input type="hidden" name="accion" value="borrar">
        <button type="submit" class="secundario">Borrar cookies de preferencias</button>
    </form>
    <p class="nota">La cookie de sesión, necesaria para el carrito, es independiente de estas preferencias. Para la práctica, inspecciona ambas en DevTools → Application/Storage → Cookies.</p>
</div>
<?php pie(); ?>
