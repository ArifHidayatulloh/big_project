<?php

namespace App\Exports;

use App\Models\WorkingList;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class WorkListExcel implements FromView, WithStyles
{
    protected $status;
    protected $depCode;
    protected $pic;
    protected $from_date;
    protected $to_date;

    // Konstruktor untuk menerima parameter filter
    public function __construct($status, $depCode, $pic, $from_date, $to_date)
    {
        $this->status = $status;
        $this->depCode = $depCode;
        $this->pic = $pic;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        // Query data berdasarkan filter
        $query = WorkingList::with(['commentDepheads.updatePics']);

        // Memeriksa status
        if ($this->status && is_array($this->status) && count($this->status) > 0 && !empty($this->status[0])) {
            $query->whereIn('status', $this->status); // Menggunakan whereIn untuk beberapa status
        }

        if ($this->depCode) {
            $query->where('dep_code', $this->depCode);
        }

        if ($this->pic) {
            $query->where('pic', $this->pic);
        }

        // Filter by created_at date range
        if ($this->from_date && $this->to_date) {
            $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
        } elseif ($this->from_date) {
            $query->whereDate('created_at', '>=', $this->from_date);
        } elseif ($this->to_date) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        $workingLists = $query->get();

        // Return view to be used in Excel
        return view('exports.working_list', compact('workingLists'));
    }

    // Apply styling and rowspan to the worksheet
    public function styles(Worksheet $sheet)
    {
        // Menambahkan gaya pada header
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFF0']
            ]
        ]);

        // Memberikan border pada seluruh tabel
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:$highestColumn$highestRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Menambahkan gaya pada baris status berdasarkan nilainya
        foreach ($sheet->getColumnIterator() as $column) {
            foreach ($column->getCellIterator() as $cell) {
                if ($cell->getValue() == 'Done') {
                    $sheet->getStyle($cell->getCoordinate())->applyFromArray([
                        'font' => ['color' => ['argb' => 'FF00FF00']],
                    ]);
                } elseif ($cell->getValue() == 'On Progress') {
                    $sheet->getStyle($cell->getCoordinate())->applyFromArray([
                        'font' => ['color' => ['argb' => 'FFFFA500']],
                    ]);
                } elseif ($cell->getValue() == 'Outstanding') {
                    $sheet->getStyle($cell->getCoordinate())->applyFromArray([
                        'font' => ['color' => ['argb' => 'FFFF0000']],
                    ]);
                }
            }
        }

        // Set auto width untuk kolom agar lebih rapi
        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
