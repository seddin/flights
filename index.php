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
        <title>Flights</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="header">
            <div class="menu">
                <a href="/" class="logo">Flights</a>
            </div>
        </div>

        <div class="search_box">
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

                <input type="date" id="departure_date" placeholder="Departure date" title="Departure Date">
                <input type="date" id="return_date" placeholder="Return date" title="Return Date">

                <button type="submit" id="search_btn">Search</button>
                <img src="loading.gif" id="loading" height="28px" class="dnone" />
            </form>
        </div>

        <br>

        <div class="found_flights">
            <div id="outbound_flights">
                <div class="flight">
                    <span>No search has been executed yet...</span>
                </div>
            </div>
            <div id="return_flights">
            </div>
        </div>

        <br>

        <div class="book_flight">
            <button onclick="bookFlight();">Book flight</button>
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
                                    <form>
                                        <input type="hidden" name="out_date" value="${flight.date}">
                                        <input type="hidden" name="out_from" value="${flight.depart.airport.name}">
                                        <input type="hidden" name="out_dep_hour" value="${datetime.getHours()}:${datetime.getMinutes()}">
                                        <input type="hidden" name="out_to" value="${flight.arrival.airport.name}">
                                        <input type="hidden" name="out_ar_hour" value="${arrivaltime.getHours()}:${arrivaltime.getMinutes()}">
                                        <input type="hidden" name="out_price" value="${flight.price}">
                                    </form>
                                    <div class="out">
                                        ${flight.depart.airport.name}
                                        <br>
                                        ${datetime.getHours()}:${datetime.getMinutes()}
                                    </div>

                                    <div class="ar">
                                        ${flight.arrival.airport.name}
                                        <br>
                                        ${arrivaltime.getHours()}:${arrivaltime.getMinutes()}
                                    </div>

                                    <div class="price">
                                        ${flight.date} <br>
                                        ${flight.price} €
                                    </div>
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
                                    <form>
                                        <input type="hidden" name="ret_date" value="${flight.date}">
                                        <input type="hidden" name="ret_from" value="${flight.depart.airport.name}">
                                        <input type="hidden" name="ret_dep_hour" value="${datetime.getHours()}:${datetime.getMinutes()}">
                                        <input type="hidden" name="ret_to" value="${flight.arrival.airport.name}">
                                        <input type="hidden" name="ret_ar_hour" value="${arrivaltime.getHours()}:${arrivaltime.getMinutes()}">
                                        <input type="hidden" name="ret_price" value="${flight.price}">
                                    </form>
                                    <div class="out">
                                        ${flight.depart.airport.name}
                                        <br>
                                        ${datetime.getHours()}:${datetime.getMinutes()}
                                    </div>

                                    <div class="ar">
                                        ${flight.arrival.airport.name}
                                        <br>
                                        ${arrivaltime.getHours()}:${arrivaltime.getMinutes()}
                                    </div>

                                    <div class="price">
                                        ${flight.date} <br>
                                        ${flight.price} €
                                    </div>
                                </div>
                            `);
                        });

                        attachClicEvent();

                        $('#loading').hide();
                        $('#search_btn').show();
                    });
                }
            });



            function attachClicEvent () {
                $('#outbound_flights .flight').on('click', function (e) {
                    $('#outbound_flights .flight').removeClass("selected");

                    $(e.currentTarget).addClass('selected');
                });

                $('#return_flights .flight').on('click', function (e) {
                    $('#return_flights .flight').removeClass("selected");

                    $(e.currentTarget).addClass('selected');
                });
            }

            function bookFlight () {
                var outFlight = $("#outbound_flights .flight.selected").find('form');
                outFlight = $(outFlight).html();

                var retFlight = $("#return_flights .flight.selected").find('form');
                retFlight = $(retFlight).html();

                if (outFlight && retFlight) {
                    $('#flight_form').remove();
                    $(document.body).append(`
                        <form id="flight_form" method="POST" action="book_flight.php">
                            ${outFlight}
                            ${retFlight}
                        </form>
                    `);

                    $('#flight_form').submit();
                }
            }
        </script>
    </body>
</html>