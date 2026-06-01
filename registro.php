<?php

$archivo = "usuarios.json";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!$nombre || !$email || !$password) {
        die("Todos los campos son obligatorios");
    }

    $usuarios = [];

    if (file_exists($archivo)) {
        $usuarios = json_decode(file_get_contents($archivo), true);
    }

    foreach ($usuarios as $u) {
        if ($u["email"] === $email) {
            die("Este email ya está registrado");
        }
    }

    $nuevoUsuario = [
        "id" => uniqid(),
        "nombre" => $nombre,
        "email" => $email,
        "password" => password_hash($password, PASSWORD_DEFAULT),
        "estado" => "pendiente",
        "fecha_registro" => date("Y-m-d H:i:s")
    ];

    $usuarios[] = $nuevoUsuario;

    file_put_contents(
        $archivo,
        json_encode($usuarios, JSON_PRETTY_PRINT)
    );

    echo "Registro enviado correctamente. Espera aprobación.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
</head>
<body>

<h2>Registro de usuario</h2>

<form method="POST">

    <input
        type="text"
        name="nombre"
        placeholder="Nombre"
        required
    >

    <br><br>

    <input
        type="email"
        name="email"
        placeholder="Email"
        required
    >

    <br><br>

    <input
        type="password"
        name="password"
        placeholder="Contraseña"
        required
    >

    <br><br>

    <button type="submit">
        Registrarme
    </button>

</form>

</body>
</html>
