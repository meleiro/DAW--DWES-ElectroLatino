
<?php

require_once __DIR__ . '/includes/config.php';

cabecera('Preferencias');

$visitante = '';

?>

<section class="intro">
    <p class="etiqueta">COOKIES EN EL CLIENTE</p>

    <h1>Preferencias del visitante</h1>

    <p>
        En esta página podrás configurar tus preferencias
        de navegación, como tu nombre y el tema visual.
    </p>
</section>

<div class="panel estrecho">

    <form method="post">

        <?= campoCsrf() ?>

        <input
            type="hidden"
            name="accion"
            value="guardar"
        >

        <label for="visitante">
            Nombre (opcional)
        </label>

        <input
            type="text"
            id="visitante"
            name="visitante"
            maxlength="40"
            value=""
            placeholder="Ej.: Ana"
        >

        <label for="tema">
            Tema visual
        </label>

        <select id="tema" name="tema">

            <option value="claro" selected>
                Claro
            </option>

            <option value="oscuro">
                Oscuro
            </option>

        </select>

        <button type="submit">
            Guardar preferencias
        </button>

    </form>

    <form method="post" class="separado">

        <?= campoCsrf() ?>

        <input
            type="hidden"
            name="accion"
            value="borrar"
        >

        <button
            type="submit"
            class="secundario"
        >
            Borrar cookies de preferencias
        </button>

    </form>

    <p class="nota">
        Las preferencias del visitante se almacenarán
        mediante cookies del navegador.
    </p>

</div>

<?php

pie();
