@extends('Backend.layouts.master');
@section('title')
Order Details
@endsection
@section('page-content')
 <!-- BEGIN: Content-->
 <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                <h3 class="content-header-title mb-0 d-inline-block">Order Detail</h3>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a>
                            </li>
                            <li class="breadcrumb-item"><a href="#">Order</a>
                            </li>
                            <li class="breadcrumb-item active">Order Detail
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
                            <h2>Order Detail</h2>
                         <b> <p class="pb-sm-3">Order No:</b> <span>{{$order->order_no}}</span></p>
                        </div>
                    </div>
                    <!-- Invoice Company Details -->

                    <!-- Invoice Customer Details -->
                    <div id="invoice-customer-details" class="row pt-2">
                        <div class="col-12 text-center text-sm-left">
                            <b class="text-muted">Address:</b>
                        </div>
                        <div class="col-sm-6 col-12 text-center text-sm-left user-info">
                            <ul class="px-0 list-unstyled">
                                <li class="text-bold-800">{{$order->name}}</li>
                                <li class="address">{{$order->address}}</li> <!-- Apply address class here -->
                            </ul>
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
                                            <th class="text-right">Item per Cred</th>
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
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
<!-- END: Content-->
@endsection
