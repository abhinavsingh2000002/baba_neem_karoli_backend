@extends('Backend.layouts.master')
@section('title')
Order Products
@endsection
@section('page-content')
 <!-- BEGIN: Content-->
 <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title">Order Products</h3>
                <div class="row breadcrumbs-top">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a>
                            </li>
                            <li class="breadcrumb-item"><a href="#">Order</a>
                            </li>
                            <li class="breadcrumb-item active">Order Products
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12">
                <div class="btn-group float-md-right">
                    <a id="cart" class="btn btn-info mb-1">
                        <i class="fa fa-shopping-cart"></i> Cart
                    </a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Add Doctors Form Wizard -->

            <section id="add-doctors">
                <div class="icon-tabs">
                    <div class="row">
                        <div class="col-12 sidebar-content d-none d-lg-block sidebar-shop">
                            <div class="card">
                                <div class="card-body">
                                    <div class="search">
                                        <input id="basic-search" type="text" placeholder="Search here..." class="basic-search">
                                        <i class="ficon ft-search"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div><select name="" id="distributor" class="form-control">
                        <option value="">Please Select Distributor</option>
                        @foreach ($distributor as $dis)
                        <option value="{{$dis->id}}">{{$dis->name}}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="row"  id="productsContainer">
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content">
                                <ul id="paginationLinks" class="pagination justify-content-center pagination-separate pagination-flat">
                                    <!-- Pagination links will be generated here dynamically -->
                                </ul>
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
let currentPage = 1; // Track the current page

// Function to load products for a specific page
function loadProduct(page = 1) {
    const searchQuery = $('.basic-search').val();
    const distributor=$('#distributor').val();
    $.ajax({
        url: '{{Route('admin_order.product_listing')}}',
        type: 'POST',
        dataType: 'json',
        data: { page: page,
            search: searchQuery,
            distributor:distributor,
         }, // Pass the current page number
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let productsContainer = $('#productsContainer'); // Assuming this is the container div
            let paginationLinks = $('#paginationLinks'); // Container for pagination buttons
            productsContainer.empty(); // Clear previous content
            paginationLinks.empty(); // Clear previous pagination buttons

            // Check if the response contains products
            if (response && response.products && Array.isArray(response.products)) {
                // Loop through the response and dynamically add product cards
                response.products.forEach(function(data) {
                    let images = data.product_image.split('|'); // Split product images

                    let carouselItems = images.map((image, index) => `
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <img class="img-fluid mb-1" src="{{ asset('storage/products/') }}/${image}" alt="Product Image ${index + 1}">
                        </div>
                    `).join('');
                    let productDetailUrl = `{{ url('admin_order/productDetails') }}/${data.id}/${data.user_id}`;
                    let productCard = `
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                            <div class="card pull-up">
                                <div class="card-content">
                                    <div class="card-body">
                                        <a href="${productDetailUrl}">
                                            <div id="productCarousel${data.id}" class="carousel slide" data-ride="carousel">
                                                <div class="carousel-inner">
                                                    ${carouselItems}
                                                </div>
                                                <!-- Controls -->
                                                <a class="carousel-control-prev" href="#productCarousel${data.id}" role="button" data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#productCarousel${data.id}" role="button" data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
                                            <b class="company-name">${data.company_name}</b>
                                            <h4 class="product-title">${data.product_name}</h4>
                                            <p class="product-description">${data.product_description.length > 10 ? data.product_description.substring(0, 10) + '...' : data.product_description}</p>
                                            <div class="price-reviews">
                                                <span class="price-box mr-2">
                                                    <span class="price"><i class="fa-solid fa-indian-rupee-sign"></i> ${data.price}</span>
                                                </span>
                                                <div class="product-details">
                                                    <b>Weight: ${data.product_quantity}</b>
                                                </div>
                                                <div class="mt-1"> <b>Items in per cred: ${data.item_per_cred}</b></div>
                                            </div>
                                        </a>
                                        <div class="quantity-input mt-2">
                                            <label for="quantity${data.id}">Quantity:</label>
                                            <input type="number" id="quantity${data.id}" pattern="^(1|[2-9]|[1-9][0-9])(\.5)?$" name="quantity" step="0.5" class="form-control" min="1" value="${data.quantity}">
                                            <button type="button" class="btn btn-primary mt-2" id="addToCart">
                                                <i class="ft-shopping-cart"></i> Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    productsContainer.append(productCard);
                });

                // Pagination Links
                if (response.totalPages > 1) {
                    let prevDisabled = response.currentPage === 1 ? 'disabled' : '';
                    let nextDisabled = response.currentPage === response.totalPages ? 'disabled' : '';

                    let paginationHtml = `
                        <li class="page-item ${prevDisabled}">
                            <a class="page-link" href="#" aria-label="Previous" id="prevPage">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                    `;

                    for (let i = 1; i <= response.totalPages; i++) {
                        let activeClass = i === response.currentPage ? 'active' : '';
                        paginationHtml += `
                            <li class="page-item ${activeClass}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>
                        `;
                    }

                    paginationHtml += `
                        <li class="page-item ${nextDisabled}">
                            <a class="page-link" href="#" aria-label="Next" id="nextPage">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    `;

                    paginationLinks.append(paginationHtml);
                }
            } else {
                console.error("Invalid response format or products are not defined");
            }
        },
        error: function(xhr, status, error) {
            console.log("Error", error);
        }
    });
}

// Handle Pagination Clicks
$(document).on('click', '#prevPage', function(e) {
    e.preventDefault();
    if (currentPage > 1) {
        currentPage--;
        loadProduct(currentPage); // Load the previous page
    }
});

$(document).on('click', '#nextPage', function(e) {
    e.preventDefault();
    if (currentPage < totalPages) {
        currentPage++;
        loadProduct(currentPage); // Load the next page
    }
});

$(document).on('click', '.page-link[data-page]', function(e) {
    e.preventDefault();
    const selectedPage = $(this).data('page');
    currentPage = selectedPage;
    loadProduct(currentPage); // Load the selected page
});

$(document).on('input', '.basic-search', function(e) {
    e.preventDefault();
    loadProduct();
});

// Initial load
$('#distributor').on('change',function(e){
    loadProduct();
});
</script>
<script>
$(document).on('click', '#addToCart', function(e) {
    e.preventDefault();
    const productCard = $(this).closest('.card-body');  // Get the closest product card
    const productId = productCard.find('a').attr('href').split('/').slice(-2, -1)[0]; // Extract product ID from URL
    const quantity = productCard.find('input[name="quantity"]').val(); // Get the entered quantity
    const distributor=$('#distributor').val();

    const quantityPattern = /^[1-9][0-9]*(\.5)?$/;  // regex for 1.5, 2.5, 3.5, etc.

    if (!quantityPattern.test(quantity)) {
        alert('Please enter a valid quantity (e.g., 1.5, 2.5, 3.5).');
        return; // Stop execution if the quantity is invalid
    }


    // Send the product ID and quantity to the server
    $.ajax({
        url: '{{ route('admin_order.add_to_cart') }}',  // Your route for adding to cart
        type: 'POST',
        dataType: 'json',
        data: {
            product_id: productId,
            quantity: quantity,
            distributor:distributor
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if(response.success) {
                alert('Product added to cart successfully!');
            } else if(response.update) {
                alert('Product Quantity Updated successful at cart')
            }
            else{
                alert('Failed to add product to cart.');
            }
        },
        error: function(xhr, status, error) {
            console.log("Error:", error);
        }
    });
});

$(document).on('click', '#cart', function(e) {
    e.preventDefault();
    var distributor = $('#distributor').val(); // Get distributor value
    if(distributor){
        var href = "{{ route('admin_order.cart_index') }}" + '?distributor=' + distributor; // Append distributor to URL
          // Redirect to the route with the distributor parameter
        window.location.href = href;
    }
    else{
        alert('Please Select Distributor');
    }

});

</script>

@endsection
