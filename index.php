<?php
    $auth = base64_encode("php-applicant:Z7VpVEQMsXk2LCBc");
    $context = stream_context_create([
        "http" => [
            "header" => "Authorization: Basic $auth"
        ]
    ]);

    $json = file_get_contents("http://tstapi.duckdns.org/api/json/1F/flightroutes/", false, $context);
    $flights = json_decode($json, true);

    $departure_places = [];

    foreach ($flights["flightroutes"] as $flight) {
        if (!$departure_places[$flight["DepCode"]]) {
            $departure_places[$flight["DepCode"]] = [
                "DepName" => $flight["DepName"],
                "DepCountry" => $flight["DepCountry"]
            ];
        }
    }
?>
<html>
    <head>
        <title>Pruebas</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <style>
            * {
                padding: 0;
                margin: 0;
                box-sizing: border-box;
            }
            body {
                width: 100%;
                padding: 10px;
            }
            form {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr 1fr 1fr;
                grid-gap: 10px;
                color: #222;
            }
            .found_flights {
                width: 100%;
                border-top: 1px dotted #94a687;
                padding-top: 10px;
            }
            .outbound_flights, .return_flights {
                /*border: 1px solid red;*/
            }
            .flight {
                width: 100%;
                min-height: 30px;
                background-color: #c0cfb6;
                border: 1px solid #94a687;
                border-radius: 4px;
                padding: 10px;
            }
            .dnone {
                display: none;
            }
        </style>
    </head>
    <body>
        <a href="/">HOMEEEEEEEEEEEEEEEEEEEEEEEEEE</a>
        <div>
            <h2>Choose a Flight:</h2>
            <br>
            <form id="search">
                <select id="from">
                    <option value="">From...</option>
                    <?php
                        foreach ($departure_places as $k => $deparPlace) {
                            echo '<option value="'.$k.'">'.$deparPlace["DepName"] . ' - ' . $deparPlace["DepCountry"] .'</option>';
                        }
                    ?>
                </select>

                <select id="to">
                    <option value="">To...</option>
                </select>

                <input type="date" id="departure_date" placeholder="Departure date">
                <input type="date" id="return_date" placeholder="Return date">

                <button type="submit" id="search_btn">Search</button>
                <img src="loading.gif" id="loading" height="28px" class="dnone" />
            </form>
        </div>

        <br>

        <div class="found_flights">
            <div class="outbound_flights">
                <h3>Outbound</h3>

                <div id="outbound_flights">
                    <div class="flight">
                        jsdgkjasdgfjsd
                    </div>
                </div>
            </div>
            <div class="return_flights">
                <h3>Return</h3>

                <div id="return_flights">
                    <div class="flight">
                        jsdgkjasdgfjsd
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $('#from').on('change', function (e) {
                var from = $('#from').val();

                $.post('app.php', {type: 1, from: from}, function (res) {
                    var data = JSON.parse(res);
                    $('#to').html('<option value="">To...</option>');

                    $(data).each(function (i) {
                        $('#to').append(` <option value="${data[i].RetCode}">${data[i].RetName} - ${data[i].RetCountry}</option>`);
                    })
                });
            });

            $('#search').on('submit', function (e) {
                e.preventDefault();

                var from = $('#from').val();
                var to = $('#to').val();
                var departure_date = $('#departure_date').val();
                var return_date = $('#return_date').val();

                if (from.length > 0) {

                    $('#search_btn').hide();
                    $('#loading').show();

                    var data = {
                        type: 2,
                        from: from,
                        to: to,
                        departure_date: departure_date,
                        return_date: return_date
                    };

                    $.post('app.php', data, function (res) {

                        var flights = JSON.parse(res);

                        $('#outbound_flights').html('');
                        $('#return_flights').html('');

                        $(flights.OUT).each(function (i) {
                            var flight = flights.OUT[i];
                            var datetime = new Date(flight.datetime);
                            var duration = flight.duration.split(":");

                            var arrivaltime = new Date(flight.datetime);
                            arrivaltime.setHours(arrivaltime.getHours() + parseInt(duration[0]));
                            arrivaltime.setMinutes(arrivaltime.getMinutes() + parseInt(duration[1]));
                            arrivaltime.setSeconds(arrivaltime.getSeconds() + parseInt(duration[2]));

                            $('#outbound_flights').append(`
                                <div class="flight">
                                    <input type="radio" id="selected_out_flight">
                                    ${flight.date}
                                    :
                                    ${flight.depart.airport.name} ${datetime.getHours()}:${datetime.getMinutes()}
                                    to
                                    ${flight.arrival.airport.name} ${arrivaltime.getHours()}:${arrivaltime.getMinutes()}
                                    for
                                    ${flight.price} €
                                </div>
                            `);
                        });

                        $(flights.RET).each(function (i) {
                            var flight = flights.RET[i];
                            var datetime = new Date(flight.datetime);
                            var duration = flight.duration.split(":");

                            var arrivaltime = new Date(flight.datetime);
                            arrivaltime.setHours(arrivaltime.getHours() + parseInt(duration[0]));
                            arrivaltime.setMinutes(arrivaltime.getMinutes() + parseInt(duration[1]));
                            arrivaltime.setSeconds(arrivaltime.getSeconds() + parseInt(duration[2]));

                            $('#return_flights').append(`
                                <div class="flight">
                                    <input type="radio" id="selected_ret_flight">
                                    ${flight.date}
                                    :
                                    ${flight.depart.airport.name} ${datetime.getHours()}:${datetime.getMinutes()}
                                    to
                                    ${flight.arrival.airport.name} ${arrivaltime.getHours()}:${arrivaltime.getMinutes()}
                                    for
                                    ${flight.price} €
                                </div>
                            `);
                        });

                        $('#loading').hide();
                        $('#search_btn').show();
                    });
                }
            });
        </script>
    </body>
</html>