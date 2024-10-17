@extends('Backend.layouts.master')
@section('title')
    Driver Task Listing
@endsection
@section('page-content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">Driver Task List</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Driver Task</a>
                                </li>
                                <li class="breadcrumb-item active">All Driver Task
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
            <div class="content-body">
                <!-- List Of All Patients -->
                <div class="users-list-table">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <!-- datatable start -->
                                <div class="table-responsive">
                                    <div id="button-container"></div>
                                    <table id="driver_task" class="table">
                                        <thead>
                                            <tr>
                                                <th>SNo</th>
                                                <th>Order Number</th>
                                                <th>Driver Name</th>
                                                <th>Driver Image</th>
                                                <th>Distributor Name</th>
                                                <th>Distributor Image</th>
                                                <th>Status</th>
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
            dataTable = $('#driver_task').DataTable({
                "processing": true,
                "serverSide": true,
                "lengthMenu": [10, 50, 100, 500, 1000],
                "ajax": {
                    "url": "{{ Route('admin_driver_task.listing') }}",
                    "type": "GET",
                    "data": function(d) {
                    }
                }
            });

            $("#button-container").html('<a href="{{ route('admin_driver_task.add') }}" id="custom-button" class="btn btn-primary mb-2">Add New +</a>');
        }
        loadData();
    </script>
@endsection
