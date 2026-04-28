<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NewsArticle;

$id = '019da62b-7394-725b-ac3e-90f2c8f46afb';
$article = NewsArticle::find($id);

if ($article) {
    echo "ID: " . $article->id . "\n";
    echo "Raw Attributes Content:\n";
    var_dump($article->getAttributes()['content']);
    echo "\nTranslations:\n";
    var_dump($article->getTranslations('content'));
} else {
    echo "Article not found.\n";
}
