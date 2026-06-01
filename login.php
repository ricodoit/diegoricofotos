<?php

session_start();

$archivo = "usuarios.json";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $usuarios = json_decode(
        file_get_contents($archivo),
        true
    );

    foreach ($usuarios as $usuario) {

        if (
            $usuario["email"] === $email
            &&
            password_verify(
                $password,
                $usuario["password"]
            )
        ) {

            if ($usuario["estado"] !== "activo") {

                die("Tu cuenta aún no está aprobada.");

            }

            $_SESSION["usuario"] = $usuario;

            header("Location: galeria-privada.php");
            exit;
        }
    }

    echo "Usuario o contraseña incorrectos";
}
?>

<form method="POST">

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
        Entrar
    </button>

</form>
