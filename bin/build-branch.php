<?php

function createComposerPackage($version, $zipUrl)
{
    $template = json_decode(file_get_contents(dirname(__DIR__).'/templates/composer.json.template'), true);
    
    $template['version'] = $version;

    $template['dist'] = [
        'url' => $zipUrl,
        'type' => 'zip',
    ];

    $template['source'] = [
        'url' => 'https://github.com/modxcms/revolution.git',
        'type' => 'git',
        'reference' => $version,
    ];

    $template['dist']['url'] = $zipUrl;
    $template['source']['reference'] = $version;

    return $template;
}

function buildBranch($version, $zipUrl, $outDir)
{
    $package = createComposerPackage($version, $zipUrl);
    $json = json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $written = (bool) file_put_contents($outDir.'/composer.json', $json);
    $copied = copy(dirname(__DIR__).'/templates/LICENSE.template', $outDir.'/LICENSE');

    return $written && $copied;
}

if (php_sapi_name() === 'cli') {
    $args = array_slice($argv, 1);

    $result = buildBranch(...$args);
    fwrite(STDERR, $result ? 'Success!' : 'Failure!');

    exit($result ? 0 : 1);
}
