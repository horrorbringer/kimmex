<?php

require 'vendor/autoload.php';

use Stichoza\GoogleTranslate\GoogleTranslate;

$kmPath = 'lang/km.json';
$kmJson = json_decode(file_get_contents($kmPath), true) ?: [];

$tr = new GoogleTranslate('km', 'en'); // translate to km from en
$updated = 0;

echo "Starting translation...\n";

foreach ($kmJson as $key => $value) {
    if ($key === $value) {
        try {
            $translated = $tr->translate($key);
            $kmJson[$key] = $translated;
            $updated++;
            echo "Translated: '$key' -> '$translated'\n";

            // Save every 20 translations to avoid losing progress
            if ($updated % 20 === 0) {
                file_put_contents($kmPath, json_encode($kmJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            // Be nice to the API
            usleep(200000);
        } catch (Exception $e) {
            echo "Failed to translate: $key. Error: ".$e->getMessage()."\n";
        }
    }
}

// Final save
file_put_contents($kmPath, json_encode($kmJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Finished translating $updated new strings.\n";
