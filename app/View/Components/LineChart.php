<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LineChart extends Component
{

    public $chartTitle;
    public $chartSubtitle;
    public $graphs;
    public $max;
    public $totals;
    public $labels;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($chartTitle, $chartSubtitle='', $graphs, $max, $labels, $totals=[]){
        $this->chartTitle = $chartTitle;
        $this->chartSubtitle = $chartSubtitle;
        $this->graphs = $graphs;
        $this->max = $max;
        $this->totals = $totals;
        $this->labels = $labels;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.line-chart');
    }
}
