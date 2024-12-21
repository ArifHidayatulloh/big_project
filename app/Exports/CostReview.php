<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CostReview implements FromView
{
    protected $descriptions;

    public function __construct($descriptions)
    {
        $this->descriptions = $descriptions;
    }

    public function view(): View
    {
        return view('exports.cost_review', [
            'descriptions' => $this->descriptions
        ]);
    }
}
