<?php
session_start();
include 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener el nombre del usuario
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$user_name = $user ? $user['name'] : 'Usuario';

// Procesar las solicitudes POST para agregar, actualizar o eliminar enlaces
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Agregar un enlace
    if (isset($_POST['linkType'])) {
        $linkType = $_POST['linkType'];

        if ($linkType === 'social') {
            $name = $_POST['linkName'] ?? '';
            $url = $_POST['linkUrl'] ?? '';
            if (!empty($name) && !empty($url)) {
                $stmt = $pdo->prepare("INSERT INTO links (user_id, name, url, type) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $name, $url, 'social']);
            }
        } elseif ($linkType === 'whatsapp') {
            $name = 'WhatsApp';
            $number = $_POST['whatsappNumber'] ?? '';
            $url = 'https://wa.me/' . preg_replace('/\D/', '', $number);
            if (!empty($number)) {
                $stmt = $pdo->prepare("INSERT INTO links (user_id, name, url, type) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $name, $url, 'whatsapp']);
            }
        } elseif ($linkType === 'file' && isset($_FILES['linkFile'])) {
            $file = $_FILES['linkFile'];
            $name = $_POST['linkFileName'] ?? '';
            $targetDir = 'uploads/';
            $fileName = time() . '_' . basename($file['name']);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                $url = $targetFilePath;
                $stmt = $pdo->prepare("INSERT INTO links (user_id, name, url, type) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $name, $url, 'file']);
            }
        }
    }

    // Actualizar un enlace existente
    elseif (isset($_POST['updateLinkId'], $_POST['updateLinkName'], $_POST['updateLinkUrl'])) {
        $updateId = $_POST['updateLinkId'];
        $updateName = $_POST['updateLinkName'];
        $updateUrl = $_POST['updateLinkUrl'];

        if (!empty($updateName) && !empty($updateUrl)) {
            $stmt = $pdo->prepare("UPDATE links SET name = ?, url = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$updateName, $updateUrl, $updateId, $user_id]);
        }
    }

    // Eliminar un enlace
    elseif (isset($_POST['deleteLinkId'])) {
        $deleteId = $_POST['deleteLinkId'];
        $stmt = $pdo->prepare("DELETE FROM links WHERE id = ? AND user_id = ?");
        $stmt->execute([$deleteId, $user_id]);
    }

    // Redireccionar para evitar reenvíos del formulario
    header('Location: dashboard.php');
    exit;
}

// Obtener los enlaces del usuario
$stmt = $pdo->prepare("SELECT * FROM links WHERE user_id = ?");
$stmt->execute([$user_id]);
$links = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
    <style>
        /* Estilos personalizados */
        body {
            margin: 0;
            padding: 0;
            background-color: rgb(10, 10, 10);
            color: rgb(240, 240, 240);
            font-family: Verdana, Tahoma, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 { margin-top: 20px; font-size: 28px; }
        form { margin: 20px auto; width: 100%; max-width: 400px; }
        .form-control, .btn { margin-bottom: 10px; }
        #links { margin-top: 20px; width: 100%; max-width: 600px; }
        .tile { background: rgb(37, 37, 37); border-radius: 10px; margin: 10px; padding: 15px; text-align: center; }
    </style>
    <script>
        function toggleFields() {
            const type = document.getElementById('linkType').value;
            document.getElementById('socialFields').style.display = type === 'social' ? 'block' : 'none';
            document.getElementById('whatsappFields').style.display = type === 'whatsapp' ? 'block' : 'none';
            document.getElementById('fileFields').style.display = type === 'file' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <h1>Bienvenido, <?= htmlspecialchars($user_name) ?>!</h1>

    <form method="POST" enctype="multipart/form-data">
        <select name="linkType" id="linkType" class="form-control" onchange="toggleFields()" required>
            <option value="" disabled selected>Selecciona el tipo de enlace</option>
            <option value="social">Red Social</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="file">Archivo (PDF)</option>
        </select>

        <div id="socialFields" style="display: none;">
            <input type="text" name="linkName" class="form-control" placeholder="Nombre del enlace">
            <input type="url" name="linkUrl" class="form-control" placeholder="URL del enlace">
        </div>

        <div id="whatsappFields" style="display: none;">
            <input type="text" name="whatsappNumber" class="form-control" placeholder="Número de WhatsApp">
        </div>

        <div id="fileFields" style="display: none;">
            <input type="text" name="linkFileName" class="form-control" placeholder="Nombre del archivo">
            <input type="file" name="linkFile" class="form-control" accept="application/pdf">
        </div>

        <button type="submit" class="btn btn-primary">Agregar Enlace</button>
    </form>

    <h2>Mis Enlaces</h2>
    <div id="links">
        <?php foreach ($links as $link): ?>
            <div class="tile">
                <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank"><?= htmlspecialchars($link['name']) ?></a>
               <form method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="updateLinkId" value="<?= htmlspecialchars($link['id']) ?>">
                    <input type="text" name="updateLinkName" class="form-control" placeholder="Nuevo Nombre" value="<?= htmlspecialchars($link['name']) ?>">
                    <input type="url" name="updateLinkUrl" class="form-control" placeholder="Nueva URL" value="<?= htmlspecialchars($link['url']) ?>">
                 <!--   <button type="submit" class="btn btn-warning">Actualizar</button>  -->
                </form>
                <form method="POST">
                    <input type="hidden" name="deleteLinkId" value="<?= htmlspecialchars($link['id']) ?>">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top: 20px;">
    <a href="profile.php?id=<?= $_SESSION['user_id'] ?>" class="btn btn-secondary">Compartir mi perfil</a>
    <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
