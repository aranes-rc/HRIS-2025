<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $size;
    public string $roundness;
    public ?string $text;
    public string $containerColor;
    public string $contentColor;
    public $sizes = [
        'xs' => 'px-2.5 py-1 text-xs',
        'sm' => 'px-2.5 py-1 text-sm',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-3.5 py-2 text-sm',
        'xl' => 'px-4 py-2.5 text-sm',
    ];
    
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $size = 'md',
        string $roundness = 'sm',
        ?string $text = null,
        string $containerColor = 'primary',
        string $contentColor = 'white',
    )
    {
        $this->size = $size;
        $this->roundness = $roundness;
        $this->text = $text;
        $this->containerColor = $containerColor;
        $this->contentColor = $contentColor;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
