<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\OrgUnit;
use Illuminate\Contracts\Console\Kernel;

$units = OrgUnit::all();
foreach ($units as $unit) {
    $curr = $unit;
    $seen = [];
    while ($curr) {
        if (in_array($curr->id, $seen)) {
            echo "CYCLE DETECTED: Unit {$unit->id} ({$unit->title}) loops at {$curr->id}\n";
            break;
        }
        $seen[] = $curr->id;
        $curr = OrgUnit::find($curr->parentId);
    }
}
echo "Check complete.\n";
