<?php

$eventType = getenv("TRAVIS_EVENT_TYPE");
if($eventType != "cron" && $eventType != "pull_request") {
    die("Not coming from a cron|pull_request");
}

$streams = json_decode(file_get_contents('tv-spain.json'));
$readmeFile = "README.md";
$readme = file_get_contents($readmeFile);
$readme = explode("## Status Update", $readme)[0];
$readme .= "## Status Update: **" . date("Y-m-d") . "**\n\n";
$readme .= "Status | Canal | URL\n--- | --- | ---\n";

foreach ($streams as $stream) {
    debug("Checking " . $stream->name . " - " . $stream->link_m3u8);
    $status = ":red_circle:";

    if (isAlive($stream->link_m3u8)) {
        $status = ":green_heart:";
    }

    $readme .= $status . "|" . $stream->name . "|" . $stream->link_m3u8 . "\n";
}

file_put_contents($readmeFile, $readme);

function debug ($line) {
    echo $line . "\n";
}

function isAlive ($url) {
    $ch = curl_init($url);
    curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode != 200) {
        debug("Error - HTTP code: " . $httpCode);
        return false;
    }
    if(substr($response, 0, 7) !== "#EXTM3U") {
        debug("Error - Response: " . $response);
        return false;
    }

    return true;
}
