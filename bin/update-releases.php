<?php

const GH_TAGS_URL = 'https://api.github.com/repos/modxcms/revolution/tags';

function fetch($url, $params)
{
    $ch = curl_init();
    $queryString = http_build_query($params);

    curl_setopt($ch, CURLOPT_URL, $url.'?'.$queryString);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: RChouinard-Modx-Satis',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    try {
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode != 200) {
            throw new \RuntimeException(sprintf('Failed fetching project releases! API responded: %s', $httpCode));
        }
    } finally {
        curl_close($ch);
    }

    return json_decode($response, true);
}

function gatherReleases()
{
    $releases = [];
    
    $page = 1;
    do {
        $response = fetch(GH_TAGS_URL, ['page' => $page, 'per_page' => 100]);
        if (count($response) > 0) {
            array_push($releases, ...$response);
        }
        $page++;
    } while (count($response) > 0);

    return $releases;
}

fwrite(STDOUT, json_encode(gatherReleases(), JSON_PRETTY_PRINT));
