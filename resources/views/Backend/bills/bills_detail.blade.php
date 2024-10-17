@extends('Backend.layouts.master')
@section('title')
Bill Detail
@endsection
@section('page-content')
 <!-- BEGIN: Content-->
 <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                <h3 class="content-header-title mb-0 d-inline-block">Invoice Template</h3>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a>
                            </li>
                            <li class="breadcrumb-item"><a href="#">Invoice</a>
                            </li>
                            <li class="breadcrumb-item active">Invoice Template
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section class="card">
                <div id="invoice-template" class="card-body p-4">
                    <!-- Invoice Company Details -->
                    <div id="invoice-company-details" class="row">
                        <div class="col-sm-6 col-12 text-center text-sm-left">
                            <div class="media row">
                                <div class="col-12 col-sm-3 col-xl-2">
                                    <img src="../../../app-assets/images/logo/logo-80x80.png" alt="company logo" class="mb-1 mb-sm-0" />
                                </div>
                                <div class="col-12 col-sm-9 col-xl-10">
                                    <div class="media-body">
                                        <ul class="ml-2 px-0 list-unstyled">
                                            <h1 class="text-bold-800">Baba Neem Karoli Traders</h1>
                                            <b>Mr.PANKAJ KESARWANI S/O KANDHAI LAL KESARWANI</b>
                                            <li>14/1, CHURCH ROAD GWALTOLI KANPUR</li>
                                            <li>Kanpur Nagar, Uttar Pradesh-208001</li>
                                            <li>INDIA</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12 text-center text-sm-right">
                            <h2>INVOICE</h2>
                            <p class="pb-sm-3">{{$order->bill_no}}</p>
                            <ul class="px-0 list-unstyled">
                                <li>Total Bill Amount</li>
                                <li class="lead text-bold-800"><i class="fa-solid fa-indian-rupee-sign"></i>{{$order->total_amount}}</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Invoice Company Details -->

                    <!-- Invoice Customer Details -->
                    <div id="invoice-customer-details" class="row pt-2">
                        <div class="col-12 text-center text-sm-left">
                            <p class="text-muted">Bill To</p>
                        </div>
                        <div class="col-sm-6 col-12 text-center text-sm-left user-info">
                            <ul class="px-0 list-unstyled">
                                <li class="text-bold-800">{{$order->name}}</li>
                                <li class="address">{{$order->address}}</li> <!-- Apply address class here -->
                            </ul>
                        </div>
                        <div class="col-sm-6 col-12 text-center text-sm-right">
                            <p><span class="text-muted">Invoice Date :</span> {{$order->order_date}}</p>
                            <p><span class="text-muted">Invoice Time :</span> {{$order->order_time}}</p>
                            {{-- <p><span class="text-muted">Due Date :</span> 10/05/2019</p> --}}
                        </div>
                    </div>
                    <!-- Invoice Customer Details -->

                    <!-- Invoice Items Details -->
                    <div id="invoice-items-details" class="pt-2">
                        <div class="row">
                            <div class="table-responsive col-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>S No</th>
                                            <th>Item & Description</th>
                                            <th class="text-right">Weight</th>
                                            <th class="text-right">Quantity</th>
                                            <th class="text-right">Item Per Cred</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orderDetail as $key =>$order)
                                        <tr>
                                            <th scope="row">{{$key+1}}</th>
                                            <td>
                                                <p>{{$order->product_name}}&ensp;&ensp; ({{$order->company_name}})</p>
                                                <p class="text-muted">{{Str::limit($order->product_description,30,'....')}}
                                                </p>
                                            </td>
                                            <td class="text-right">{{$order->product_weight}}</td>
                                            <td class="text-right">{{$order->product_quantity}}</td>
                                            <td class="text-right">{{$order->item_per_cred}}</td>
                                            <td class="text-right"><i class="fa-solid fa-indian-rupee-sign"></i>{{$order->amount}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-7 col-12 text-center text-sm-left">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="text-center">
                                            {{-- <p class="mb-0 mt-1">Authorized person</p>
                                            <img src="../../../app-assets/images/pages/signature-scan.png" alt="signature" class="height-100" />
                                            <h6>Amanda Orton</h6>
                                            <p class="text-muted">Managing Director</p> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 col-12">
                                <p class="lead">Total</p>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Sub Total</td>
                                                <td class="text-right">{{$order->total_amount}}</td>
                                            </tr>
                                            <tr>
                                                <td>TAX (12%)</td>
                                                <td class="text-right">0</td>
                                            </tr>
                                            <tr>
                                                <td class="text-bold-800"><b class="fs-4">Total </b></td>
                                                <td class="text-bold-800 text-right">{{$order->total_amount}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center">
                                    <p class="mb-0 mt-1">Authorized person</p>
                                    <img src="../../../app-assets/images/pages/signature-scan.png" alt="signature" class="height-100" />
                                    <h6>MR. PANKAJ KESARWANI</h6>
                                    <p class="text-muted">Managing Director</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Footer -->
                    <div id="invoice-footer">
                        <div class="row">
                            <div class="col-sm-7 col-12 text-center text-sm-left">
                                <h6>Terms & Condition</h6>
                                <p>Test pilot isn't always the healthiest business.</p>
                            </div>
                            <div class="col-sm-5 col-12 text-center">
                                <button type="button" class="btn btn-info btn-print btn-lg my-1"><i class="la la-paper-plane-o mr-50"></i>
                                    Print
                                    Invoice</button>
                                <a href="{{Route('admin_bills.invoicePdf',$order->id)}}" class="btn btn-info  btn-lg my-1" style="color: white;">
                                    <i class="la la-paper-plane-o mr-50"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Invoice Footer -->

                </div>
            </section>

        </div>
    </div>
</div>
<!-- END: Content-->
@endsection
@section('page-js')

@endsection
