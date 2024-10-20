<?php

namespace App\Exports;

use App\Models\Bill;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;



class LedgerExport implements FromCollection, WithHeadings,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return Bill::select('bills.bill_no', 'orders.order_no', 'bills.bill_date', 'users.name as distributor_name', 'orders.total_amount', 'orders.order_status')
            ->join('orders', 'bills.order_id', '=', 'orders.id')
            ->join('users', 'bills.user_id', '=', 'users.id')
            ->where('users.status', '=', 1)->where('orders.order_status','!=',0)
            ->whereMonth('bills.bill_date', '=', $this->month)
            ->whereYear('bills.bill_date', '=', $this->year)
            ->get()
            ->map(function ($item) {
                // Map status values to string representations
                switch ($item->order_status) {
                    case 0:
                        $item->order_status = 'Failed';
                        break;
                    case 1:
                        $item->order_status = 'Pending';
                        break;
                    case 2:
                        $item->order_status = 'Confirmed';
                        break;
                    case 3:
                        $item->order_status = 'Delivered';
                        break;
                    default:
                        $item->order_status = 'Unknown'; // Optional: Handle unexpected values
                        break;
                }
                return $item;
            });
    }

    public function headings(): array
    {
        return [
            'Bill No',
            'Order No',
            'Date & Time',
            'Distributor Name',
            'Total Amount',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Make the headings bold and increase font size
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

}
