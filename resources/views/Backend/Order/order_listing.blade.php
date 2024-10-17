@extends('Backend.layouts.master')
@section('title')
    Orders
@endsection
@section('page-content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">Orders List</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Orders</a>
                                </li>
                                <li class="breadcrumb-item active">All Orders
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
                                    <table id="order_list" class="table">
                                        <thead>
                                            <tr>
                                                <th>S NO</th>
                                                <th>Order Number</th>
                                                <th>Distributor Name</th>
                                                <th>Distributor Image</th>
                                                <th>Order Date&Time</th>
                                                <th>Order Confirm Date&Time</th>
                                                <th>Order delivered Date&TIme</th>
                                                <th>Order Failed Date&Time</th>
                                                <th>Amount</th>
                                                <th>Order Status</th>
                                                <th>Order Status Change</th>
                                                <th>Action Button</th>
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
            dataTable = $('#order_list').DataTable({
                "processing": true,
                "serverSide": true,
                "lengthMenu": [10, 50, 100, 500, 1000],
                "ajax": {
                    "url": "{{ Route('admin_order.listing') }}",
                    "type": "GET",
                    "data": function(d) {}
                }
            });

            var ancha_id = $('#ancha_id').val();
            $("#button-container").html(
                '<a href="{{ route('product.add') }}" id="custom-button" class="btn btn-primary mb-2">Add New +</a>');
        }
        loadData();
    </script>
    <script>

    $(document).on('change', '.order-status-change', function() {
    var orderId = $(this).data('order-id');
    var newStatus = $(this).val();
    var row = $(this).closest('tr');
    var statusCell = $(this).closest('td'); // Get the specific cell containing the status

    // AJAX call to update the order status
    $.ajax({
        url: '{{ route("admin_order.updateStatus") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            order_id: orderId,
            order_status: newStatus
        },
        success: function(response) {
            if (response.order_update_status) {
                alert('Order status updated successfully!');

                // Reset the status cell classes
                $(statusCell).find('.order-status-change').removeClass('bg-warning bg-info bg-success bg-danger');

                // Change the color of the dropdown based on the new status
                if (newStatus == 1) {
                    $(statusCell).find('.order-status-change').addClass('bg-warning'); // Pending
                } else if (newStatus == 2) {
                    $(statusCell).find('.order-status-change').addClass('bg-info'); // Confirmed
                } else if (newStatus == 3) {
                    $(statusCell).find('.order-status-change').addClass('bg-success'); // Delivered
                } else if (newStatus == 0) {
                    $(statusCell).find('.order-status-change').addClass('bg-danger'); // Failed
                }
            } else {
                alert('Failed to update order status.');
            }
        },
        error: function(xhr, status, error) {
            alert('Error updating order status.');
        }
    });
});
</script>
@endsection
