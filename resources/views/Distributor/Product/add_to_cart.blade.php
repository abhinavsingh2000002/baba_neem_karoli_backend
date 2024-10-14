@extends('Backend.layouts.master')
@section('title')
Shopping Cart
@endsection
@section('page-content')
   <!-- BEGIN: Content-->
   <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                <h3 class="content-header-title mb-0 d-inline-block">Shopping Cart</h3>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Shopping Cart
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12">
                <div class="btn-group float-md-right">
                    <button class="btn btn-info dropdown-toggle mb-1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                    <div class="dropdown-menu arrow"><a class="dropdown-item" href="#"><i class="fa fa-calendar-check mr-1"></i> Calender</a><a class="dropdown-item" href="#"><i class="fa fa-cart-plus mr-1"></i> Cart</a><a class="dropdown-item" href="#"><i class="fa fa-life-ring mr-1"></i> Support</a>
                        <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i class="fa fa-cog mr-1"></i> Settings</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="shopping-cart">
                <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                        <a class="nav-link active" id="shopping-cart" data-toggle="tab" aria-controls="shop-cart-tab" href="#shop-cart-tab" aria-expanded="true">Shopping Cart</a>
                    </li>
                </ul>
                <div class="tab-content pt-1">
                    <div role="tabpanel" class="tab-pane active" id="shop-cart-tab" aria-expanded="true" aria-labelledby="shopping-cart">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Details</th>
                                                    <th>No. Of Products</th>
                                                    <th>Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cartTableBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Price Details Section -->
                        <div class="row match-height">
                            <div class="col-lg-12 col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Price Details</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="price-detail">Price (<span class="itemCount">0</span> items)
                                                <span class="float-right"><i class="fa-solid fa-indian-rupee-sign"></i>
                                                    <span id="grandTotal">0</span>
                                                </span>
                                            </div>
                                            <div class="price-detail">Delivery Charges
                                                <span class="float-right"><i class="fa-solid fa-indian-rupee-sign"></i>0</span>
                                            </div>
                                            <div class="price-detail">TAX / VAT
                                                <span class="float-right"><i class="fa-solid fa-indian-rupee-sign"></i>0</span>
                                            </div>
                                            <hr>
                                            <div class="price-detail">Payable Amount
                                                <span class="float-right"><i class="fa-solid fa-indian-rupee-sign"></i>
                                                    <span id="payableAmount">0</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form action="#">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="text-right">
                                                    <a href="{{Route('distributor_product.index')}}" class="btn btn-info mr-2">Continue Shopping</a>
                                                    <a href="{{Route('order.orderPlaced')}}" class="btn btn-warning">Place Order</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="checkout-tab" aria-labelledby="checkout">
                        <div class="row">
                            <div class="col-md-4 order-md-2 mb-4">
                                <div class="card">
                                    <div class="card-header mb-3">
                                        <h4 class="card-title">Your cart (<span class="itemCount">0</span>)</h4>
                                    </div>
                                    <div class="card-content">
                                        <ul class="list-group mb-3" id="cartProduct">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 order-md-1">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Billing address</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body" id="billingAddress">
                                        </div>

                                    </div>
                                </div>
                            </div>
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
   function loadCartProducts() {
    $.ajax({
        url: '{{Route('cart.listing')}}',
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.data) {
                let cartItems = response.data;
                let cartTableBody = '';
                let checkOutCart='';
                let grandTotal = 0;

                cartItems.forEach(function(cart) {
                    let images = cart.product_image.split('|');
                    let total_price = cart.quantity * cart.price;
                    grandTotal += total_price;
                    let deleteItemRoute=`{{url('cart/delete')}}/${cart.cart_id}`;
                    cartTableBody += `
                        <tr>
                            <td>
                                <div id="carouselExample1" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img class="d-block w-100 fixed-size" src="/storage/products/${images[0]}" alt="Image 1">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="product-title">${cart.product_name}</div>
                                <div class="product-title">${cart.company_name}</div>
                                <div class="product-color"><strong>Weight : </strong> ${cart.product_quantity}</div>
                                <div class="product-size"><strong>Item in per Cred :</strong> ${cart.item_per_cred}</div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <label class="text-center mb-4" for="quantity">Quantity:
                                        <input type="number" class="quantity" id="quantity_${cart.id}" name="quantity" value="${cart.quantity}" class="form-control" min="1">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="total-price"><i class="fa-solid fa-indian-rupee-sign"></i>${total_price}</div>
                            </td>
                            <td>
                                <div class="product-action">
                                    <a href="#" id="delete" data-id=${cart.cart_id}><i class="ft-trash-2"></i></a>
                                </div>
                            </td>
                        </tr>
                    `;

                     checkOutCart +=`
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div>
                                <h6 class="my-0">${cart.product_name} X ${cart.quantity}</h6>
                                <small class="text-muted">${cart.company_name}</small>
                            </div>
                            <span class="text-muted"><i class="fa-solid fa-indian-rupee-sign"></i>${total_price}</span>
                        </li>`;
                });
                $('#cartProduct').html(checkOutCart);
                $('#cartTableBody').html(cartTableBody);

                $('#grandTotal').text(grandTotal);
                $('#payableAmount').text(grandTotal);
                $('.itemCount').text(cartItems.length);
            }
        },
        error: function(xhr, status, error) {
            console.log("Error", error);
        }
    });
}
loadCartProducts();

  $(document).on('input', '.quantity', function(e) {
    e.preventDefault();
    let cartId = $(this).attr('id').split('_')[1];
    let quantity = $(this).val();
    changeQuantity(cartId, quantity);
});

function changeQuantity(cartId, quantity) {
    $.ajax({
        url: '{{Route('cart.add_to_cart')}}',
        type: 'POST',
        dataType: 'json',
        data: {
            product_id: cartId,
            quantity: quantity
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // You can refresh the cart or update the total price based on the response
            console.log('Quantity updated successfully', response);
            loadCartProducts();  // Refresh the cart products after the update
        },
        error: function(xhr, status, error) {
            console.log('Error updating quantity', error);
        }
    });
}

$(document).on('click','#delete',function(e){
    e.preventDefault();
    let cartId=$(this).data('id');
    deleteCartProduct(cartId);
});
function deleteCartProduct(cartId){
    $.ajax({
        url:'{{Route('cart.delete')}}',
        type:'GET',
        dataType:'json',
        data:{
            id:cartId,
        },
        success:function(response){
            if(response.success){
                alert('Cart Item Deleted Successfully');
                 loadCartProducts();
            }
            else{
                alert('error while deleting cart item');
            }
        },
        error:function(xhr,status,error){
            console.log('Error',error);
        }
    });
}

</script>
@endsection
