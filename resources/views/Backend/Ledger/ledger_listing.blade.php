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
                                    <label for="monthSelect" class="form-label">Month:</label>
                                    <select id="monthSelect" class="form-control">
                                        <option value="">Select Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>

                                <div class="col-md-2 mb-2">
                                    <label for="yearSelect" class="form-label">Year:</label>
                                    <select id="yearSelect" class="form-control">
                                        <option value="">Select Year</option>
                                        <!-- Generate years dynamically -->
                                        <script>
                                            const currentYear = new Date().getFullYear();
                                            const currentMonth = new Date().getMonth() + 1; // Months are 0-indexed

                                            for (let i = currentYear; i >= currentYear - 10; i--) {
                                                document.write(`<option value="${i}">${i}</option>`);
                                            }

                                            // Set default selected month and year
                                            document.getElementById('monthSelect').value = String(currentMonth).padStart(2, '0'); // Pad month with leading zero
                                            document.getElementById('yearSelect').value = currentYear;
                                        </script>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2">
                                    <button id="filterButton" class="btn btn-primary w-100">Filter</button>
                                </div>

                                <div class="col-md-6 mb-2 mt-2 d-flex justify-content-md-end">
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
            "url": "{{ Route('admin_ledger.listing') }}",
            "type": "GET",
            "data": function(d) {
                d.month = $('#monthSelect').val();
                d.year = $('#yearSelect').val();
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
        let month = $('#monthSelect').val();
        let year = $('#yearSelect').val();
        window.location.href = `/admin_ledger/ledgerexcel?month=${month}&year=${year}&export=true`;
        });


        // PDF Export
        $('#exportPdf').on('click', function() {
            let month = $('#monthSelect').val();
            let year = $('#yearSelect').val();
            $.ajax({
                url:"{{Route('admin_ledger.ledgerpdf')}}",
                type:'GET',
                data:{
                    month:month,
                    year:year,
                },
                success:function(response){
                    if(response.no_data=='0'){
                        alert('No Data Available for search Detail');
                    }
                else {
                    // Redirect to PDF URL for download
                    window.location.href = "{{Route('admin_ledger.ledgerpdf')}}" + "?month=" + month + "&year=" + year;
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
