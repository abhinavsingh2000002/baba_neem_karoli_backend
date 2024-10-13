@extends('Backend.layouts.master')
@section('title')
    Product Profile
@endsection
@section('page-content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">Product Detail</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Product Details</a>
                                </li>
                                <li class="breadcrumb-item active">Product Detail
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="patient-profile">
                    <div class="row match-height">
                        <div class="col-lg-12 col-md-12">
                            <div class="card shadow-sm border-0 rounded">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-lg-4 col-md-4 text-center">
                                            <div id="patientImageCarousel" class="carousel slide" data-ride="carousel">
                                                <div class="carousel-inner">
                                                    @foreach ($imagesArray as $index => $image)
                                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                            <img src="{{ asset('storage/products/' . $image) }}"
                                                                alt="Product Image"
                                                                class="card-img-top mb-3 patient-img img-fluid rounded-square">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <a class="carousel-control-prev" href="#patientImageCarousel" role="button"
                                                    data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#patientImageCarousel" role="button"
                                                    data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
                                            <h4 class="patient-name">{{ $product->product_name }}</h4>
                                        </div>
                                        <div class="col-lg-8 col-md-8">
                                            <div class="patient-info">
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <div class="patient-info-heading">Product Name:</div>
                                                        <span>{{ $product->product_name }}</span>
                                                    </li>
                                                    <li>
                                                        <div class="patient-info-heading">Company Name:</div>
                                                        <span>{{ $product->company_name }}</span>
                                                    </li>
                                                    <li>
                                                        <div class="patient-info-heading">Product Quantity:</div>
                                                        <span>{{ $product->product_quantity }}</span>
                                                    </li>
                                                    <li>
                                                        <div class="patient-info-heading">Product Description:</div>
                                                        <span>{{ $product->product_description }}</span>
                                                    </li>
                                                    <li>
                                                        <div class="patient-info-heading">NO of Item per Cred:</div>
                                                        <span>{{ $product->item_per_cred }}</span>
                                                    </li>
                                                    <li>
                                                        <div class="patient-info-heading">Product Status:</div>
                                                        <span>{{ $product->status == 1 ? 'Active' : 'Inactive' }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
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
@endsection
