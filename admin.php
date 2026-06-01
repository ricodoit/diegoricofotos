<?php

$archivo = "usuarios.json";

$usuarios = json_decode(
    file_get_contents($archivo),
    true
);

if (isset($_GET["aprobar"])) {

    $id = $_GET["aprobar"];

    foreach ($usuarios as &$usuario) {

        if ($usuario["id"] === $id) {

            $usuario["estado"] = "activo";

        }
    }

    file_put_contents(
        $archivo,
        json_encode($usuarios, JSON_PRETTY_PRINT)
    );

    header("Location: admin.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Administración</title>
</head>
<body>

<h1>Usuarios registrados</h1>

<table border="1" cellpadding="10">

<tr>
    <th>Nombre</th>
    <th>Email</th>
    <th>Estado</th>
    <th>Acción</th>
</tr>

<?php foreach($usuarios as $usuario): ?>

<tr>

    <td>
        <?= htmlspecialchars($usuario["nombre"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($usuario["email"]) ?>
    </td>

    <td>
        <?= $usuario["estado"] ?>
    </td>

    <td>

        <?php if($usuario["estado"] == "pendiente"): ?>

            <a href="?aprobar=<?= $usuario["id"] ?>">
                Aprobar
            </a>

        <?php else: ?>

            Activo

        <?php endif; ?>

    </td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>
