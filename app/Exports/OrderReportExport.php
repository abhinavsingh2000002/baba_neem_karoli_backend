<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrderReportExport implements FromView
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function view(): View
    {
        $all_product = Product::where('status', 1)->get();
        $order = Order::with(['orderDetails', 'user'])
            ->where('order_date', '=', $this->date)
            ->whereHas('user', function ($query) {
                $query->where('status', 1);
            })
            ->get();

        return view('Backend.Report.report_excel', [
            'all_product' => $all_product,
            'order' => $order,
        ]);
    }
}
