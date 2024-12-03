<?php

namespace App\Exports;

use App\Models\PaymentSchedule;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentSupplierExcel implements FromView, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        // Filter data
        $query = PaymentSchedule::query();

        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('supplier_name', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['purchase_date_from'])) {
            $query->where('purchase_date', '>=', $this->filters['purchase_date_from']);
        }

        if (!empty($this->filters['purchase_date_to'])) {
            $query->where('purchase_date', '<=', $this->filters['purchase_date_to']);
        }

        if (!empty($this->filters['due_date'])) {
            $query->where('due_date', '<=', $this->filters['due_date']);
        }

        if (!empty($this->filters['startDate'])) {
            $query->whereDate('paid_date', '>=', $this->filters['startDate']);
        }

        if (!empty($this->filters['endDate'])) {
            $query->whereDate('paid_date', '<=', $this->filters['endDate']);
        }

        $paymentSchedules = $query->get();

        // Return view khusus untuk Excel
        return view('exports.payment_supplier', [
            'paymentSchedules' => $paymentSchedules
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Menambahkan gaya pada header
        $sheet->getStyle('A1:J1')->applyFromArray([
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

        // Menambahkan gaya pada kolom "status"
        foreach ($sheet->getRowIterator() as $row) {
            $cell = $sheet->getCell('G' . $row->getRowIndex());
            $value = $cell->getValue();
            if ($value == 'Paid') {
                $sheet->getStyle($cell->getCoordinate())->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF00FF00']],
                ]);
            } elseif ($value == 'Unpaid') {
                $sheet->getStyle($cell->getCoordinate())->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFFF0000']],
                ]);
            }
        }

        $sheet->getStyle('D2:D' . $highestRow)->getNumberFormat()->setFormatCode('"Rp "#,##0_-');

        // Set auto width untuk kolom agar lebih rapi
        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
