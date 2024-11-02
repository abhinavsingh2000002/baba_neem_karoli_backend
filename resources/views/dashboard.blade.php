@php
    $distributor=App\Models\User::where('role_id',2)->where('status','=',1)->count();
    $driver=App\Models\User::where('role_id',3)->where('status','=',1)->count();
    $today_order=App\Models\Order::where('order_date',Carbon\Carbon::now()->toDateString())->count();
    $all_products=App\Models\Product::where('status','=',1)->count();
    $latest_order=App\Models\Order::select('orders.*','users.name','users.image_path')->join('users','orders.user_id','=','users.id')->latest()->limit(5)->get();
@endphp

@extends('Backend.layouts.master')
@section('title')
    Dashboard
@endsection
@section('page-content')
  <!-- BEGIN: Content-->
  <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        @if(Auth::user()->role_id==1)
        <div class="content-body">
            <!-- Hospital Info cards -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="success">{{$distributor}}</h3>
                                        <h6>Distributors</h6>
                                    </div>
                                    <div>
                                      <a href="{{Route('distributor.index')}}"><i class="icon-user-follow success font-large-2 float-right"></i></a>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                    <div class="progress-bar bg-gradient-x-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="text-danger">{{$driver}}</h3> <!-- Using Bootstrap danger class for light red -->
                                        <h6>Drivers</h6>
                                    </div>
                                    <div>
                                       <a href="{{Route('driver.index')}}"><i class="la la-car text-danger font-large-2 float-right"></i></a> <!-- Changed to car icon -->
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div> <!-- Using Bootstrap danger class -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="info">{{$today_order}}</h3>
                                        <h6>Today Orders</h6>
                                    </div>
                                    <div>
                                      <a href="{{Route('admin_order.index')}}"><i class="icon-basket-loaded info font-large-2 float-right"></i></a>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                    <div class="progress-bar bg-gradient-x-info" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="warning">{{$all_products}}</h3> <!-- Example count -->
                                        <h6>All Products</h6>
                                    </div>
                                    <div>
                                       <a href="{{Route('product.index')}}"><i class="fas fa-box warning font-large-2 float-right"></i></a> <!-- Changed to package icon -->
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                    <div class="progress-bar bg-gradient-x-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hospital Info cards Ends -->

            <!-- Appointment Table -->
            <div class="row match-height">
                <div id="recent-appointments" class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Recent Orders</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a class="btn btn-sm btn-danger box-shadow-2 round btn-min-width pull-right" href="{{Route('admin_order.index')}}" target="_blank">View all</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content mt-1">
                            <div class="table-responsive">
                                <table id="recent-orders-doctors" class="table table-hover table-xl mb-0">
                                    <thead>
                                        <tr>
                                            <th class="border-top-0">Order NO</th>
                                            <th class="border-top-0">Distributor Name</th>
                                            <th class="border-top-0">Order Status</th>
                                            <th class="border-top-0">Order Date&Time</th>
                                            <th class="border-top-0">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($latest_order as $latest)
                                        <tr class="pull-up">
                                            <td class="text-truncate">{{$latest->order_no}}</td>
                                            <td class="text-truncate">{{$latest->name}}</td>
                                            <td>
                                                @if($latest->order_status==1)
                                                <button type="button" class="btn btn-sm btn-outline-warning round">Pending</button>
                                                @elseif($latest->order_status==2)
                                                <button type="button" class="btn btn-sm btn-outline-info round">Confirmed</button>
                                                @elseif($latest->order_status==3)
                                                <button type="button" class="btn btn-sm btn-outline-success round">Delivered</button>
                                                @elseif($latest->order_status==0)
                                                <button type="button" class="btn btn-sm btn-outline-danger round">Rejected</button>
                                                @endif
                                            </td>
                                            <td class="text-truncate">{{$latest->order_date.' '.$latest->order_time}}</td>
                                            <td class="text-truncate">{{$latest->total_amount}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Appointment Table Ends -->
        </div>
        @endif

    </div>
</div>
<!-- END: Content-->
@endsection
@section('page-js')

@endsection

