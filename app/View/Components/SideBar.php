<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SideBar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }
    
    /**
     * Check if the given route is active.
     *
     * @param string $route
     * @param bool $exact
     * @return bool
     */
    public function isActive($route, $exact = false)
    {
        if ($exact) {
            return request()->routeIs($route);
        }
        
        return request()->routeIs($route) || request()->routeIs($route.'.*');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
