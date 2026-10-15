<?php

function calcularDistancia($lat1, $lon1, $lat2, $lon2) {


    $radioTierra = 6371;

    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

  
    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;


    $a = sin($dLat / 2) ** 2
       + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Devolver la distancia en kilómetros
    return $radioTierra * $c;
}


$distancia = calcularDistancia(
    36.867649, -6.177709,
    36.866091, -6.176686
);

echo "Distancia en línea recta: "
    . round($distancia, 15) . " km";

?>