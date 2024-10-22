@extends('Backend.layouts.master')
@section('title')
Ledger
@endsection
@section('page-content')
  <!-- BEGIN: Content-->
  <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title">Ledger List</h3>
                <div class="row breadcrumbs-top">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Ledger</a></li>
                            <li class="breadcrumb-item active">Ledger Cred</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif
        @if (session('delete'))
            <div class="alert alert-danger text-center">{{ session('delete') }}</div>
        @endif

        <div class="content-body">
            <div class="users-list-table">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <!-- Date Filter -->
                            <div class="date-filter mb-3 row align-items-center">
                                <div class="col-md-2 mb-2">
                                    <label for="monthYear" class='form-label'>Select Month & Year </label>
                                    <input type="month" class="form-control" id="currentMonthYear">
                                    <script>
                                        const today = new Date();
                                        // Extract the year and month in YYYY-MM format
                                        const currentMonthYear = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2);
                                        // Set the default value of the input field
                                        document.getElementById('currentMonthYear').value = currentMonthYear;
                                    </script>
                                </div>
                                <div class="col-12 col-md-2">
                                    <button id="filterButton" class="btn btn-primary w-100">Filter</button>
                                </div>
                                <div class="col-12 col-md-3">
                                </div>
                                <div class="col-md-5 mb-2 mt-2 d-flex justify-content-md-end">
                                    <button id="exportCsv" class="btn btn-success me-2">Download CSV</button>
                                    <button id="exportPdf" class="btn btn-danger">Download PDF</button>
                                </div>
                            </div>


                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table id="ledger_list" class="table">
                                    <thead>
                                        <tr>
                                            <th>S no</th>
                                            <th>Bill Number</th>
                                            <th>Order Number</th>
                                            <th>Bill Date & Time</th>
                                            <th>Distributor Name</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <!-- DataTable ends -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->
@endsection
@section('page-js')
<script>
  function loadData() {
    dataTable = $('#ledger_list').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthMenu": [10, 50, 100, 500, 1000],
        "ajax": {
            "url": "{{ Route('distributor_ledger.listing') }}",
            "type": "GET",
            "data": function(d) {
                d.month = $('#monthSelect').val();
                d.year = $('#yearSelect').val();
                d.distributorName=$('#distributorName').val();
                d.currentMonthYear=$('#currentMonthYear').val();
            }
        }
    });
}

        // Event listener for filter button
        $('#filterButton').on('click', function() {
            dataTable.ajax.reload(); // Reload data with filters
        });

        // CSV Export
        $('#exportCsv').on('click', function() {
        let currentMonthYear=$('#currentMonthYear').val();
        let [year, month] = currentMonthYear.split('-');
        year = parseInt(year);
        month = parseInt(month);
        let distributorName=$('#distributorName').val();

        window.location.href = `/distributor_ledger/ledgerexcel?month=${month}&year=${year}`;
        });


        // PDF Export
        $('#exportPdf').on('click', function() {
            let distributorName=$('#distributorName').val();
            let currentMonthYear=$('#currentMonthYear').val();
            let [year, month] = currentMonthYear.split('-');
            year = parseInt(year);
            month = parseInt(month);
            $.ajax({
                url:"{{Route('distributor_ledger.ledgerpdf')}}",
                type:'GET',
                data:{
                    month:month,
                    year:year,
                    distributorName:distributorName,
                },
                success:function(response){
                    if(response.no_data=='0'){
                        alert('No Data Available for search Detail');
                    }
                else {
                    // Redirect to PDF URL for download
                    window.location.href = "{{Route('distributor_ledger.ledgerpdf')}}" + "?month=" + month + "&year=" + year;
                }
                },
                error:function(xhr,status,error){
                    console.error('Error generating PDF:', error);
                }

            })
        });

        loadData();

</script>
@endsection
