<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($title) ? $title . ' - ' . APP_NAME : APP_NAME ?>
    </title>
    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/style.css">
</head>

<body>

    <div class="glass-wrapper">
        <!-- Background Blobs -->
        <div class="glass-blob blob-1"></div>
        <div class="glass-blob blob-2"></div>

        <div class="container" style="padding-top: 40px;">
            <?= $content ?>
        </div>
    </div>

    <script src="<?= URL_ROOT ?>/assets/js/app.js"></script>
</body>

</html>