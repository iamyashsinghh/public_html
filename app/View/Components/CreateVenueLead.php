<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CreateVenueLead extends Component {

    public string $url;
    public function __construct(string $urlForSubmit) {
        $this->url = $urlForSubmit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render() {
        return view('components.create-venue-lead');
    }
}
