@extends('Backend.layouts.master')
@section('title')
    Cred Listing
@endsection
@section('page-content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">Cred List</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Cred</a>
                                </li>
                                <li class="breadcrumb-item active">All Cred
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
                                    <table id="cred_list" class="table">
                                        <thead>
                                            <tr>
                                                <th>S no</th>
                                                <th>Driver Name</th>
                                                <th>Driver Image</th>
                                                <th>Cred Date & Time</th>
                                                <th>Cred In</th>
                                                <th>Cred Out</th>
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
        function loadData() {
            // Initialize DataTable
            dataTable = $('#cred_list').DataTable({
                "processing": true,
                "serverSide": true,
                "lengthMenu": [10, 50, 100, 500, 1000],
                "ajax": {
                    "url": "{{ Route('distributor_cred.listing') }}",
                    "type": "GET",
                    "data": function(d) {
                    }
                }
            });
        }
        loadData();
    </script>
@endsection
