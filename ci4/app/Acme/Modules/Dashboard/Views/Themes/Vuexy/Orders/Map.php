<?php

//die(json_encode($params["markers"])); exit;

$markers = $params["markers"];

$cmarkers = $params["cmarkers"];

?>
<!-- Dashboard Ecommerce Starts -->
<section >
    <div id="map" class="row" style="width: 84vw;height: 77vh;position: relative;overflow: hidden;"></div>
</section>
<!-- Dashboard Ecommerce ends -->
<script>


    function createMarker(options, html) {
        var marker = new google.maps.Marker(options);
        bounds.extend(options.position);
        if (html) {
            google.maps.event.addListener(marker, "click", function () {
                infoWindow.setContent(html);
                infoWindow.open(options.map, this);
                map.setZoom(map.getZoom() + 1)
                map.setCenter(marker.getPosition());
            });
        }
        return marker;
    }

    function initialize() {

        var locations = [
            ['<b>MAMBO\'S CHICKEN - PARK STREET</b><br>17 Park Street<br>Harare, Zimbabwe', -17.8291538,31.0444739, 'red'],
                <?php foreach($markers as $marker){ ?>['<h3><b><?= $marker["name"]; ?></b></h3><h4><b>REG: <?= $marker["reg"]; ?></b></h4><hr/><?php foreach($marker["orders"] as $k=>$v){ ?><?= "<h4>Ref#: <b>$k</b>, Status: <b><code>On the Way</code></b></h4><hr/>"; ?><?php } ?><?php if(isset($marker["updated"])){ echo "<h5>Last Update:".date('d-m-Y H:i:s', ((int) $marker["updated"]/1000)); } ?></h5>', <?= $marker["position"]; ?>, 'green'],<?php } ?>
                <?php foreach($cmarkers as $marker){ ?>['<h3><b><?= $marker["name"]; ?></b></h3><h4><b>REG: <?= $marker["reg"]; ?></b></h4><hr/><?php foreach($marker["orders"] as $k=>$v){ ?><?= "<h4>Ref#: <b>$k</b>, Status: <b><code>On The Way</code></b></h4><hr/>"; ?><?php } ?><?php if(isset($marker["updated"])){ echo "<h5>Last Update:".date('d-m-Y H:i:s', ((int) $marker["updated"]/1000)); } ?></h5>', <?= $marker["position"]; ?>, 'green'],<?php } ?>
            ['<b>MAMBO\'S CHICKEN - HEAD QUARTERS</b><br>Joina City<br>Harare, Zimbabwe', -17.8259267,31.0480151, 'blue']
        ];

        window.map = new google.maps.Map(document.getElementById('map'), {
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();

        var bounds = new google.maps.LatLngBounds();

        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                icon: {
                    path: "M27.648 -41.399q0 -3.816 -2.7 -6.516t-6.516 -2.7 -6.516 2.7 -2.7 6.516 2.7 6.516 6.516 2.7 6.516 -2.7 2.7 -6.516zm9.216 0q0 3.924 -1.188 6.444l-13.104 27.864q-0.576 1.188 -1.71 1.872t-2.43 0.684 -2.43 -0.684 -1.674 -1.872l-13.14 -27.864q-1.188 -2.52 -1.188 -6.444 0 -7.632 5.4 -13.032t13.032 -5.4 13.032 5.4 5.4 13.032z",
                    scale: 0.6,
                    strokeWeight: 0.2,
                    strokeColor: 'black',
                    strokeOpacity: 1,
                    fillColor: locations[i][3],
                    fillOpacity: 0.85,
                },
                map: map
            });

            bounds.extend(marker.position);

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                map.setZoom(map.getZoom() + 1)
                map.setCenter(marker.getPosition());
                return function () {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }

        map.fitBounds(bounds);

        var listener = google.maps.event.addListener(map, "idle", function () {
            map.setZoom(3);
            google.maps.event.removeListener(listener);
        });

        //-17.8278408,31.0474932

    }

    function loadScript() {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' + 'callback=initialize&key=AIzaSyCVP6N800aBz9lXz0mpJUE270jHX0OuUrk';
        document.body.appendChild(script);
    }

    window.onload = loadScript;
</script>
