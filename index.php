<?php require 'api/core.php'; ?>
<?php date_default_timezone_set('America/Sao_Paulo'); ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A TSA é uma empresa de autopeças especializada na fabricação de sensores de nível de combustível.">
    <meta name="author" content="Igor Henrique Constant">

    <base href="<?php echo Request::getBaseUrl(); ?>">

    <title>TSA do Brasil</title>


    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>

    <?php
    extract($_GET);
    require 'header.php';
    require 'pages/' . $page . '.php';
    require 'footer.php';
    ?>


    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>