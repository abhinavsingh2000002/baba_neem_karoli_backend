@extends('Backend.layouts.master')
@section('title')
    Order Edit
@endsection
@section('page-content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- users edit start -->
                <section class="users-edit">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="account" aria-labelledby="account-tab"
                                        role="tabpanel">
                                        <!-- users edit media object start -->
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        @if (session('success'))
                                        <div class="alert alert-success text-center">
                                            {{ session('success') }}
                                        </div>
                                        @endif
                                        <!-- resources/views/orders/edit.blade.php -->
                                        <form action="{{ route('admin_order.update', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <h3 class="text-center mb-2">Order ID: {{ $order->order_no }}</h3>

                                            <!-- Loop through each product in the order details -->
                                            @foreach ($order->orderDetails as $detail)
                                                <div class="product-row row mb-3">
                                                    <input type="hidden" name="details[{{ $detail->id }}][id]" value="{{ $detail->id }}">

                                                    <div class="col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="product_no_{{ $detail->id }}">Product Name</label>
                                                            <input type="text" class="form-control" id="product_no_{{ $detail->id }}"
                                                                value="{{ $detail->product_name }}" readonly required>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="product_name_{{ $detail->id }}">Product Name</label>
                                                            <input type="text" class="form-control" id="product_name_{{ $detail->id }}"
                                                                value="{{ $detail->product_name }}" readonly required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="company Name{{ $detail->id }}">Company Name</label>
                                                            <input type="text" class="form-control" id="company_name{{ $detail->id }}"
                                                                value="{{ $detail->company_name }}" readonly required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="product Weight{{ $detail->id }}">Product Weight</label>
                                                            <input type="text" class="form-control" id="product_weight{{ $detail->id }}"
                                                                value="{{ $detail->product_weight }}" readonly required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="company Name{{ $detail->id }}">Item Per Cred</label>
                                                            <input type="text" class="form-control" id="company_name{{ $detail->id }}"
                                                                value="{{ $detail->item_per_cred }}" readonly required>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="quantity_{{ $detail->id }}">Quantity</label>
                                                            <input type="number" class="form-control" id="quantity_{{ $detail->id }}"
                                                                name="details[{{ $detail->id }}][quantity]"
                                                                value="{{ $detail->product_quantity }}" 
                                                                pattern="^[0-9]*\.?5?$"
                                                                min="0.5" step="0.5" required
                                                                oninput="validateQuantity(this)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" name="details[{{ $detail->id }}][delete]"
                                                                value="1" id="delete-{{ $detail->id }}">
                                                            <label class="form-check-label" for="delete-{{ $detail->id }}">Remove Product</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr style="border: 1px solid rgb(66, 63, 63);"> <!-- Horizontal line to separate each product -->
                                            @endforeach

                                            <!-- Add new product section -->
                                            <div id="new-products" class="mb-3">
                                                <h4>Add New Products</h4>
                                                <button type="button" id="add-product" class="btn btn-primary">Add Product</button>
                                                <!-- Product inputs for new products will be dynamically added here -->
                                            </div>

                                            <button type="submit" class="btn btn-success">Update Order</button>
                                        </form>
                                        <!-- users edit account form ends -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- users edit ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->
@endsection
@section('page-js')
<script>
  document.getElementById('add-product').addEventListener('click', function() {
    const productOptions = `
        @foreach($products as $product)
        <option value="{{ $product->id }}">
            {{ $product->product_no }} / {{ $product->product_name }} / {{ $product->company_name }} / {{ $product->product_quantity }} / {{ $product->item_per_cred }}
        </option>
        @endforeach
    `;

    const newProductTemplate = `
        <div class="product-row row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="new_product">Product</label>
                    <select name="new_products[product_id][]" class="form-control" required>
                        ${productOptions}
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="new_quantity">Quantity</label>
                    <input type="number" name="new_products[quantity][]" class="form-control" required>
                </div>
            </div>
    `;
    document.getElementById('new-products').insertAdjacentHTML('beforeend', newProductTemplate);
});

function validateQuantity(input) {
    // First remove any non-numeric characters except decimal point
    input.value = input.value.replace(/[^0-9.]/g, '');
    
    // Convert to number
    let value = parseFloat(input.value);
    
    // Check if it's a valid number
    if (!isNaN(value)) {
        // Round to nearest 0.5
        value = Math.round(value * 2) / 2;
        
        // Update input value
        input.value = value;
    }
}
function validateQuantity(input) {
    // First remove any non-numeric characters except decimal point
    input.value = input.value.replace(/[^0-9.]/g, '');
    
    // Convert to number
    let value = parseFloat(input.value);
    
    // Check if it's a valid number
    if (!isNaN(value)) {
        // Round to nearest 0.5
        value = Math.round(value * 2) / 2;
        
        // Update input value
        input.value = value;
    }
    
    // If value is less than 0.5, set to 0.5
    if (value < 0.5) {
        input.value = "0.5";
    }
}

</script>
@endsection
