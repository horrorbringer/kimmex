<?php

namespace App\View\Components;

use App\Support\PageViewCounter;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageViewCount extends Component
{
    public int $count;

    public function __construct(?string $path = null, public bool $light = false)
    {
        $this->count = PageViewCounter::count($path ?? request()->path());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.page-view-count');
    }
}
