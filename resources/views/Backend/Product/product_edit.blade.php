@extends('Backend.layouts.master')
@section('title')
    Edit Products
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
                                        <h1 class="mb-3">Edit Products</h1>
                                        <form action="#" method="POST"
                                            enctype="multipart/form-data" id="driver-form">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" name="id" id="" value="{{$product->id}}">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label>Product Name<span class="danger"> *</span></label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Product Name" name="product_name" value="{{$product->product_name}}" required
                                                                data-validation-required-message="This Product Name field is required">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label>Company Name <span class="danger"> *</span></label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Company Name" name="company_name" value="{{$product->company_name}}" required
                                                                data-validation-required-message="This Company Name field is required">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label for="address">Description <span class="danger">
                                                                    *</span></label>
                                                            <textarea class="form-control" name="product_description" id="product_description" rows="6">{{$product->product_description}}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label>Weight<span class="danger"> *</span></label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Aadhar Number" name="product_quantity" value="{{$product->product_quantity}}" required
                                                                data-validation-required-message="This Aadhar Number field is required">
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>No of Items per cred<span class="danger"> *</span></label>
                                                                <input type="text" class="form-control" value="{{$product->item_per_cred}}"
                                                                    placeholder="No of Item" name="no_of_item" required
                                                                    data-validation-required-message="This Aadhar Number field is required">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Dropzone for multiple image upload -->
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="images">Upload Images</label>
                                                        <div id="image-dropzone" class="dropzone"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                                    <button type="submit"
                                                        class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Submit</button>
                                                    <button type="reset" class="btn btn-light">Cancel</button>
                                                </div>
                                            </div>
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
        Dropzone.autoDiscover = false; // Prevent Dropzone from auto-initializing

        var myDropzone = new Dropzone("#image-dropzone", {
            url: "{{ Route('product.update') }}", // Form submission URL
            autoProcessQueue: false, // Don't automatically upload
            uploadMultiple: true,
            parallelUploads: 10, // Number of files to upload at a time
            maxFiles: 10, // Maximum number of files to accept
            acceptedFiles: 'image/*', // Only accept images
            addRemoveLinks: true, // Show links to remove files
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}" // Add CSRF token to the headers
            },

            init: function() {
                var myDropzone = this;


                document.querySelector("button[type=submit]").addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var formData = new FormData(document.getElementById('driver-form'));

                    myDropzone.files.forEach(function(file) {
                        formData.append('images[]', file);
                    });


                    fetch(myDropzone.options.url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Product created successfully.")
                                window.location.href =
                                    "{{ Route('product.index') }}";
                            } else {
                                alert("Error: " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert("Error uploading files.");
                        });

                });

                myDropzone.on("error", function(file, response) {
                    alert("Error uploading file: " + response);
                });
            }
        });
    </script>
@endsection
