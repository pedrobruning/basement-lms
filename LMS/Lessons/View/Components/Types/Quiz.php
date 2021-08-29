<?php

namespace LMS\Lessons\View\Components\Types;

use Illuminate\View\Component;

class Quiz extends Component
{

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('lessons::components.types.quiz');
    }
}
