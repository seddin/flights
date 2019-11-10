<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $out_date = $_POST["out_date"];
        $out_from = $_POST["out_from"];
        $out_dep_hour = $_POST["out_dep_hour"];
        $out_to = $_POST["out_to"];
        $out_ar_hour = $_POST["out_ar_hour"];
        $out_price = $_POST["out_price"];

        $ret_date = $_POST["ret_date"];
        $ret_from = $_POST["ret_from"];
        $ret_dep_hour = $_POST["ret_dep_hour"];
        $ret_to = $_POST["ret_to"];
        $ret_ar_hour = $_POST["ret_ar_hour"];
        $ret_price = $_POST["ret_price"];

//        var_dump($_POST);
    } else {
        die();
    }
?>

<html>
    <head>
        <meta name="charset" content="utf-8">
        <title>Flights Overview</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container">
            <div class="flight_overview">
                <div class="title">
                    <h2>Flights Overview</h2>
                </div>
                <br>
                
                <h4>Outbound Flight</h4>
                <div>
                    <p><?= $out_date; ?></p>
                    <p><?= $out_from; ?> -> <?= $out_to; ?></p>
                    <p><?= $out_dep_hour; ?> -> <?= $out_ar_hour; ?></p>
                    <p><?= $out_price; ?> € per person</p>
                </div>

                <br>

                <h4>Return Flight</h4>
                <div>
                    <p><?= $ret_date; ?></p>
                    <p><?= $ret_from; ?> -> <?= $ret_to; ?></p>
                    <p><?= $ret_dep_hour; ?> -> <?= $ret_ar_hour; ?></p>
                    <p><?= $ret_price; ?> € per person</p>
                </div>

                <br>

                <h4>Passengers</h4>
                <div>

                </div>

                <br>

                <h4>Total</h4>
                <div>
                    <p><?= $out_price + $ret_price; ?> €</p>
                </div>
            </div>
        </div>

    </body>
</html>
