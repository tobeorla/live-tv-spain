<?php

$streams = json_decode(file_get_contents('../tv-spain.json'));
$readmeFile = "../README.md";
$readme = file_get_contents($readmeFile);
$readme = explode("## Status Update", $readme)[0];
$readme .= "## Status Update: **" . date("Y-m-d") . "**\n\n";
$readme .= "Status | Canal | URL\n--- | --- | ---\n";
$i = 0;

foreach ($streams as $stream) {
    echo "Checking " . $stream->name . " - " . $stream->link_m3u8 . "\n";
    $status = ":red_circle:";

    if (isAlive($stream->link_m3u8)) {
        $status = ":green_heart:";
    }

    $readme .= $status . "|" . $stream->name . "|" . $stream->link_m3u8 . "\n";
$i++;if($i == 10) {break;}
}

file_put_contents($readmeFile, $readme);

function isAlive ($url) {
    $ch = curl_init($url);
    curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode != 200 ||substr($response, 0, 7) !== "#EXTM3U") {
        return false;
    }

    return true;
}
