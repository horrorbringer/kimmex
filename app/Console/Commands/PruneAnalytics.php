<?php

namespace App\Console\Commands;

use App\Models\PageView;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PruneAnalytics extends Command
{
    protected $signature = 'analytics:prune {--days=90 : Keep data for this many days}';
    protected $description = 'Delete page view records older than N days to prevent disk bloat';

    public function handle(): void
    {
        $days = (int) $this->option('days');
        $cutoff = Carbon::now()->subDays($days);

        $deleted = PageView::where('visited_at', '<', $cutoff)->delete();

        $this->info("Pruned {$deleted} page view records older than {$days} days.");
    }
}
