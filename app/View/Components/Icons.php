<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Icons extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $src;
    public $fill;

    public $class;
    public $style;


    public function __construct($src, $fill, $class, $style)
    {
        $this->src = $src;
        $this->fill = $fill;
        $this->class = $class;
        $this->style = $style;

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.icons');
    }
}
