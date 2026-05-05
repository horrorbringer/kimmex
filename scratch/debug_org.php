<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OrgUnit;

$units = OrgUnit::with('employee')->get();
foreach ($units as $u) {
    echo "ID: {$u->id} | Parent: " . ($u->parentId ?: 'ROOT') . " | Name: " . ($u->employee?->name ?: 'N/A') . " | Title: " . json_encode($u->getTranslations('title')) . " | Active: " . ($u->isActive ? 'Y' : 'N') . "\n";
}
