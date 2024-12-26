<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CostReviewConsolidated implements FromView
{
    protected $descriptions;
    protected $months;
    protected $year;

    public function __construct($descriptions, $months, $year)
    {
        $this->descriptions = $descriptions;
        $this->months = $months;
        $this->year = $year;
    }

    public function view(): View
    {
        return view('exports.cost_review_consolidated', [
            'descriptions' => $this->descriptions,
            'months' => $this->months,
            'year' => $this->year
        ]);
    }
}
