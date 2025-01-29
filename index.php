<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Bienvenido a Linktree</title>
</head>
<body>
    <div class="container text-center" style="margin-top: 50px;">
        <h1>Bienvenido a Linktree</h1>
        <p>Agrega y comparte todos tus enlaces en un solo lugar.</p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn btn-primary btn-lg" style="margin: 10px;">Ir a mi Dashboard</a>
            <a href="logout.php" class="btn btn-danger btn-lg" style="margin: 10px;">Cerrar sesión</a>
        <?php else: ?>
            <a href="register.php" class="btn btn-success btn-lg" style="margin: 10px;">Registrar</a>
            <a href="login.php" class="btn btn-info btn-lg" style="margin: 10px;">Iniciar sesión</a>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
