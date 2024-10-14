@extends('Backend.layouts.master')
@section('title')
Order Listing
@endsection
@section('page-content')
 <!-- BEGIN: Content-->
 <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                <h3 class="content-header-title mb-0 d-inline-block">Order</h3>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Order
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="shopping-cart">
                <div class="tab-content pt-1">
                    <div class="tab-pane active" id="comp-order-tab" aria-expanded="true" role="tablist" aria-labelledby="complete-order">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 mb-2 d-flex justify-content-between align-items-start">
                                <div class="card w-100">
                                    <div class="card-body">
                                        <div class="search">
                                            <div class="input-group">
                                                <input id="basic-search" type="text" placeholder="Search here..." class="form-control basic-search">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="ficon ft-search"></i> <!-- Replace with your preferred icon class -->
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 mb-2 d-flex justify-content-between align-items-start">
                                <div class="card w-100">
                                    <div class="card-body">
                                        <div class="search">
                                            <input type="date" id="date-filter-input" class="form-control date-filter-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="orderContainer"></div>

                        <!-- Pagination Links -->
                        <ul id="paginationLinks" class="pagination justify-content-center mt-3"></ul>

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
   function orderListing(page = 1) {
    const searchQuery = $('.basic-search').val();
    const searchQueryDate = $('.date-filter-input').val();
    $.ajax({
        url: '{{Route('order.listing')}}?page=' + page,  // Append the current page number
        type: 'POST',
        dataType: 'json',
        data: { search: searchQuery, searchDate: searchQueryDate },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let html = '';
            if (response.data) {
                response.data.forEach((ord, key) => {
                    // Determine the status class for the row background color
                    let statusClass = '';
                    switch (ord.order_status) {
                        case 0: // Failed
                            statusClass = 'bg-dark-danger'; // Darker Light Red
                            break;
                        case 1: // Pending
                            statusClass = 'bg-dark-warning'; // Darker Light Yellow
                            break;
                        case 2: // Confirmed
                            statusClass = 'bg-dark-info'; // Darker Light Blue
                            break;
                        case 3: // Delivered
                            statusClass = 'bg-dark-success'; // Darker Light Green
                            break;
                        default:
                            statusClass = ''; // Default class
                    }

                    html += `
                        <div class="card ${statusClass}"> <!-- Apply status class here -->
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-12 col-sm-2 mb-2">
                                            <b class="order-title">S No</b>
                                            <div class="order-info">${(response.from + key)}</div>
                                        </div>
                                        <div class="col-12 col-sm-2 mb-2">
                                            <b class="order-title">Order Number</b>
                                            <div class="order-info">${ord.order_no}</div>
                                        </div>
                                        <div class="col-12 col-sm-2 mb-2">
                                            <b class="order-title">Date & Time</b>
                                            <div class="order-info">${ord.order_date} ${ord.order_time}</div>
                                        </div>
                                        <div class="col-12 col-sm-2 mb-2">
                                            <b class="order-title">Amount Paid</b>
                                            <div class="order-info">${ord.total_amount}</div>
                                        </div>
                                        <div class="col-12 col-sm-2 mb-2">
                                            <b class="order-title">Order Status</b>
                                            <div class="order-info">${getOrderStatus(ord.order_status)}</div>
                                        </div>
                                        <div class="col-12 col-sm-2 mb-2">
                                            <a href="/order/listingDetail/${ord.id}" class="btn btn-primary text-white">
                                                <i class="fas fa-info-circle"></i> Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Update the order container with new content
                $('#orderContainer').html(html);

                // Generate pagination links
                generatePagination(response);
            } else {
                html = `
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body text-center">
                                <h5>No orders found</h5>
                            </div>
                        </div>
                    </div>
                `;
                $('#orderContainer').html(html);
                $('#paginationLinks').html('');
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
}

function generatePagination(data) {
    let paginationHtml = '';

    if (data.prev_page_url) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="orderListing(${data.current_page - 1})">Previous</a></li>`;
    }

    for (let i = 1; i <= data.last_page; i++) {
        paginationHtml += `<li class="page-item ${data.current_page === i ? 'active' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="orderListing(${i})">${i}</a>
        </li>`;
    }

    if (data.next_page_url) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="orderListing(${data.current_page + 1})">Next</a></li>`;
    }

    $('#paginationLinks').html(paginationHtml);  // Update pagination links
}

function getOrderStatus(status) {
    switch (status) {
        case 0:
            return 'Failed';
        case 1:
            return 'Pending';
        case 2:
            return 'Confirmed';
        case 3:
            return 'Delivered';
        default:
            return 'Unknown Status';
    }
}

// Initial call to load the orders
orderListing();

// Search Bar
$(document).on('input','#basic-search',function(e){
    e.preventDefault();
    orderListing();
});

$(document).on('input','#date-filter-input',function(e){
    e.preventDefault();
    orderListing();
});

</script>
@endsection
