<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $bio = substr($_POST['bio'], 0, 50); // Limitar a 50 caracteres

    // Verificar si el nombre de usuario ya está registrado
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        echo "<script>alert('El nombre de usuario ya está en uso. Por favor elige otro.');</script>";
    } else {
        $profileImage = null;

        // Manejar la carga de la imagen de perfil
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $targetDir = "uploads/profile_images/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['profile_image']['name']);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                $profileImage = $targetFilePath;
            }
        }

        // Insertar nuevo usuario
        $stmt = $pdo->prepare("INSERT INTO users (username, name, password, bio, profile_image) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $name, $password, $bio, $profileImage])) {
            echo "<script>alert('Cuenta creada con éxito. Puedes iniciar sesión ahora.'); window.location.href = 'login.php';</script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: rgb(10, 10, 10);
            color: rgb(240, 240, 240);
            display: flex;
            align-items: center;
            flex-direction: column;
            width: 100vw;
            font-family: Verdana, Tahoma, sans-serif;
            height: 100vh; /* Full height */
        }

        .container {
            width: 90%;
            max-width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            background-color: rgb(37, 37, 37);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .container:hover {
            transform: translateY(-5px); /* Slight lift on hover */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .form-control {
            border-radius: 5px;
            background-color: rgb(52, 52, 52);
            color: rgb(240, 240, 240);
            border: 1px solid rgb(80, 80, 80);
            transition: border-color 0.2s;
        }

        .form-control::placeholder {
            color: rgb(200, 200, 200);
        }

        .form-control:focus {
            border-color: rgb(100, 100, 100);
            background-color: rgb(40, 40, 40);
            color: rgb(240, 240, 240);
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        .btn {
            background-color: rgb(240, 240, 240);
            color: rgb(0, 0, 0);
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn:hover {
            background-color: rgb(220, 220, 220);
            transform: translateY(-2px); /* Lift effect on hover */
        }

        .text-info {
            color: rgb(240, 240, 240);
        }

        .alert {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Usuario" required>
            </div>
            <div class="form-group">
                <input type="text" name="name" class="form-control" placeholder="Nombre" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <div class="form-group">
                <textarea name="bio" class="form-control" placeholder="Escribe una biografía (máximo 50 caracteres)" maxlength="50" required></textarea>
            </div>
            <div class="form-group">
                <label for="profile_image">Foto de perfil</label>
                <input type="file" name="profile_image" class="form-control-file" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">Registrar</button>
        </form>
    </div>
</body>
</html>
