<?php
session_start();
include 'config.php';

$user_id = $_GET['id'] ?? null;

if ($user_id === null) {
    echo "Usuario no encontrado.";
    exit;
}

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Usuario no encontrado.";
    exit;
}

// Obtener enlaces del usuario
$stmt = $pdo->prepare("SELECT * FROM links WHERE user_id = ?");
$stmt->execute([$user_id]);
$links = $stmt->fetchAll();

// Arreglo que relaciona las redes sociales con sus respectivos íconos
$socialIcons = [
    'facebook.com' => 'assets/images/facebook.png',
    'twitter.com' => 'assets/images/twitter.png',
    'instagram.com' => 'assets/images/instagram.png',
    'onlyfans.com' => 'assets/images/onlyfans.png',
    'spotify.com' => 'assets/images/spotify.png',
    'tiktok.com' => 'assets/images/tiktok.png',
    'youtube.com' => 'assets/images/youtube.png',
    'linkedin.com' => 'assets/images/linkedin.png',
    'github.com' => 'assets/images/github.png',
    'hackerrank.com' => 'assets/images/hackerrank.png',
    'replit.com' => 'assets/images/replit.png',
    'credly.com' => 'assets/images/credly.png',
    'pdf' => 'assets/images/pdf.png',
    'wa.me' => 'assets/images/whatsapp.png'

];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/webp">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicon.png">
    <title>Perfil Público - Linktree</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <style>
        .page-item-wrap {
            background-color: rgba(255, 255, 255, 0.8); /* Fondo blanco atenuado */
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .back-to-dashboard {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .back-to-dashboard:hover {
            background-color: #0056b3;
        }

        /* Estilo para la biografía */
        .bio-box {
    background-color: rgba(0, 0, 0, 0.3); /* Fondo oscuro semi-transparente */
    color: #fff; /* Color de texto blanco */
    padding: 20px;
    border-radius: 8px; /* Bordes redondeados */
    margin: 20px auto; /* Centrado con margen */
    max-width: 600px; /* Tamaño máximo */
    text-align: center; /* Centrado del texto */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra suave */
    word-wrap: break-word; /* Ajuste de palabra larga */
    overflow-wrap: break-word; /* Asegura que las palabras largas no se salgan del recuadro */
}

.bio-text {
    font-size: 16px; /* Tamaño de fuente adecuado */
    line-height: 1.5; /* Espaciado entre líneas */
    word-wrap: break-word; /* Asegura el ajuste de palabras largas */
}
    </style>
</head>
<body>
    <!-- Animación de fondo -->
    <section class="animated-background">
        <div id="stars1"></div>
        <div id="stars2"></div>
        <div id="stars3"></div>
    </section>

    <div class="min-h-full flex-h-center" id="background_div">
        <div class="mt-48 page-full-wrap relative">
            <!-- Mostrar imagen de perfil -->
            <img class="display-image m-auto" src="<?= $user['profile_image'] ? $user['profile_image'] : 'assets/images/logo.png' ?>" alt="Imagen de perfil"/>
            <h2 class="page-title page-text-color page-text-font mt-16 text-center">
                <?= htmlspecialchars($user['name']) ?> 
            </h2>
            <!-- Mostrar biografía -->
<div class="bio-box">
    <p class="bio-text"><?= htmlspecialchars($user['bio']) ?></p>
</div>


            <div class="mt-24">
                <?php if (count($links) > 0): ?>
                    <?php foreach ($links as $link): ?>
                        <?php
                        $url = strtolower($link['url']);
                        $iconPath = '';

                        // Buscar el ícono correspondiente a la URL
                        foreach ($socialIcons as $social => $icon) {
                            if (strpos($url, $social) !== false) {
                                $iconPath = $icon;
                                break;
                            }
                        }
                        ?>

                        <div class="page-item-wrap relative">
                            <a target="_blank" class="page-item-each py-10 flex-both-center" href="<?= htmlspecialchars($link['url']) ?>">
                                <img class="link-each-image" src="<?= htmlspecialchars($iconPath ?: 'assets/images/default.png') ?>" alt="Ícono"/>
                                <span class="item-title text-center"> <?= htmlspecialchars($link['name']) ?> </span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($links as $link): ?>
    <?php if ($link['type'] === 'file'): ?>
        <div class="tile">
                     
        </div>
    <?php endif; ?>
<?php endforeach; ?>

                <?php else: ?>
                    <p>No se encontraron enlaces.</p>
                <?php endif; ?>
            </div>

            <!-- Botón de regreso al dashboard -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="back-to-dashboard">Regresar al Dashboard</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Snowfall Background Script (descomentarlo si es necesario) -->
    <!-- <script src="assets/js/snowfall.js"></script> -->
</body>
</html>
