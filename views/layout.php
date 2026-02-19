<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/css/styles.css">

    <?php
        foreach ($extra_css as $css_file) { ?>
            <link rel="stylesheet" href="assets/css/<?=$css_file?>">
        <?php }
    ?>

    <title><?= htmlspecialchars($title) ?></title>
</head>
<body class="d-flex flex-column min-vh-100">

    <?php 
        if($withMenu)
            include __DIR__ . '/partials/header.php'; 
    ?>

    <main class="flex-fill d-flex align-items-center">
        <div class="container">
                    <?=$content?>
        </div>
    </main>

    <?php 
        if($withMenu)
            include __DIR__ . '/partials/footer.php'; 
    ?>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>
</body>
</html>
