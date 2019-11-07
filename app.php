<?php
    // username:password
    $user = file_get_contents("user.txt");
    $auth = base64_encode($user);

    $context = stream_context_create([
        "http" => [
            "header" => "Authorization: Basic $auth"
        ]
    ]);

    $type = $_POST["type"];


    if ($type == 1) {
        $from = $_POST["from"];

        $json = file_get_contents("http://tstapi.duckdns.org/api/json/1F/flightroutes/?departureairport=$from", false, $context);
        $data = json_decode($json, true);

        echo json_encode($data["flightroutes"]);
    }

    if ($type == 2) {
        $from = $_POST["from"];
        $to = $_POST["to"];
        $departure_date = str_replace("-", "", $_POST["departure_date"]);
        $return_date = str_replace("-", "", $_POST["return_date"]);

        $url = "http://tstapi.duckdns.org/api/json/1F/flightavailability/";
        $p = "?";
        $p .= "departuredate=$departure_date";
        $p .= "&departureairport=$from";
        $p .= "&destinationairport=$to";
        $p .= "&returndepartureairport=$to";
        $p .= "&returndestinationairport=$from";
        $p .= "&returndate=$return_date";

        $json = file_get_contents($url.$p, false, $context);
        $data = json_decode($json, true);

        echo json_encode($data["flights"]);
    }