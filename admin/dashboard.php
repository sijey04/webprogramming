<?php
    session_start();

    // Check if the user is logged in and has the necessary permissions
    if (!isset($_SESSION['account']) || !$_SESSION['account']['is_admin']) {
        
        header('HTTP/1.0 403 Forbidden');
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Forbidden</title>
        </head>
        <body>
            <h1>Forbidden</h1>
            <p>You don\'t have permission to access this resource.</p>
            <hr>
            <address>Apache/2.4.41 (Ubuntu) Server at beta.example.com Port 80</address>
        </body>
        </html>';
        exit;
    }

    require_once '../includes/head.php';
?>
<body id="dashboard">
    <div class="wrapper">
        <?php
            require_once '../includes/topnav.php';
            require_once '../includes/sidebar.php';
        ?>
        <div class="content-page px-3">
            <!-- dynamic content here -->
        </div>
    </div>
    <?php
        require_once '../includes/footer.php';
    ?>
</body>
</html>
