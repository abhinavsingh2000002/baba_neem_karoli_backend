@extends('Backend.layouts.master')
@section('title')
    Map Product Price
@endsection
@section('page-content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">Map Product Price List</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{Route('map_product_price.index')}}">Map Product Price</a>
                                </li>
                                <li class="breadcrumb-item active">All Map Product Price
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('delete'))
                <div class="alert alert-danger text-center">
                    {{ session('delete') }}
                </div>
            @endif
            <div class="content-body">
                <!-- List Of All Patients -->
                <div class="users-list-table">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <!-- datatable start -->
                                <div class="table-responsive">
                                    <div id="button-container"></div>
                                    <table id="map_product_price" class="table">
                                        <thead>
                                            <tr>
                                                <th>S No</th>
                                                <th>Product No</th>
                                                <th>Product Name</th>
                                                <th>Distributor Name</th>
                                                <th>Distributor Image</th>
                                                <th>Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- datatable ends -->
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
@section('page-js')
    <script>
        // Function to load data into DataTable
        function loadData() {
            // Initialize DataTable
            dataTable = $('#map_product_price').DataTable({
                "processing": true,
                "serverSide": true,
                "lengthMenu": [10, 50, 100, 500, 1000],
                "ajax": {
                    "url": "{{ Route('map_product_price.listing') }}",
                    "type": "GET",
                    "data": function(d) {}
                }
            });

            var ancha_id = $('#ancha_id').val();
            $("#button-container").html(
                '<a href="{{ route('map_product_price.add') }}" id="custom-button" class="btn btn-primary mb-2">Add New +</a>');
        }
        loadData();
    </script>
@endsection
