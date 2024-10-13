@extends('Backend.layouts.master')
@section('title')
    Distributor Profile
@endsection
@section('page-content')
 <!-- BEGIN: Content-->
 <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title"> Distributor Detail</h3>
                <div class="row breadcrumbs-top">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a>
                            </li>
                            <li class="breadcrumb-item"><a href="#">Distributor</a>
                            </li>
                            <li class="breadcrumb-item active"> Distributor Profile
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
                                        <div class="patient-img-name">
                                            <img src="{{$data->image_path}}" alt="Patient Image" class="card-img-top mb-3 patient-img img-fluid rounded-circle">
                                            <h4 class="patient-name">{{$data->name}}</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-8">
                                        <div class="patient-info">
                                            <ul class="list-unstyled">
                                                <li>
                                                    <div class="patient-info-heading">Birth:</div>
                                                    <span>{{$data->dob}}</span>
                                                </li>
                                                <li>
                                                    <div class="patient-info-heading">Email:</div>
                                                    <span>{{$data->email}}</span>
                                                </li>
                                                <li>
                                                    <div class="patient-info-heading">Contact:</div>
                                                    <span>{{$data->mobile}}</span>
                                                </li>
                                                <li>
                                                    <div class="patient-info-heading">Address:</div>
                                                    <span>{{$data->address}}</span>
                                                </li>
                                                <li>
                                                    <div class="patient-info-heading">Aadhar Number:</div>
                                                    <span>{{$data->aadhar_number}}</span>
                                                </li>
                                                <li>
                                                    <div class="patient-info-heading">Pan Number:</div>
                                                    <span>{{$data->pan_number}}</span>
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
