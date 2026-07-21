<?php

$directories = ['app', 'resources/views'];
$strings = [];

foreach ($directories as $dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php'])) {
            $content = file_get_contents($file->getPathname());

            // Match __('...')
            preg_match_all("/__\(\s*'([^']+)'\s*\)/", $content, $matches1);
            if (! empty($matches1[1])) {
                foreach ($matches1[1] as $match) {
                    $strings[] = $match;
                }
            }

            // Match __('\"...\"')
            preg_match_all('/__\(\s*"([^"]+)"\s*\)/', $content, $matches2);
            if (! empty($matches2[1])) {
                foreach ($matches2[1] as $match) {
                    $strings[] = $match;
                }
            }

            // Match @lang('...')
            preg_match_all("/@lang\(\s*'([^']+)'\s*\)/", $content, $matches3);
            if (! empty($matches3[1])) {
                foreach ($matches3[1] as $match) {
                    $strings[] = $match;
                }
            }

            // Match @lang(\"...\")
            preg_match_all('/@lang\(\s*"([^"]+)"\s*\)/', $content, $matches4);
            if (! empty($matches4[1])) {
                foreach ($matches4[1] as $match) {
                    $strings[] = $match;
                }
            }
        }
    }
}

$strings = array_unique($strings);
$kmPath = 'lang/km.json';
$kmJson = json_decode(file_get_contents($kmPath), true) ?: [];

$missingCount = 0;
foreach ($strings as $string) {
    if (! array_key_exists($string, $kmJson)) {
        $kmJson[$string] = $string; // set to english by default
        $missingCount++;
    }
}

file_put_contents($kmPath, json_encode($kmJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Added $missingCount missing strings to $kmPath";
