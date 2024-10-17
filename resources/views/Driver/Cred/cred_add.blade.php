@extends('Backend.layouts.master')
@section('title')
Add Cred
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
                                <div class="tab-pane active" id="account" aria-labelledby="account-tab" role="tabpanel">
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
                                    <form action="{{Route('driver_cred.create')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <!-- users edit media object ends -->
                                        <!-- users edit account form start -->
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Distributor <span class="danger"> *</span></label>
                                                    <select class="form-control" name="distributor_name">
                                                        <option value="">-------Please Distributor Role------</option>
                                                        @foreach ($user as $data )
                                                        <option value="{{$data->id}}">{{$data->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                            <div class="col-12 col-sm-12">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="cred"> Cred Out <span class="danger"> *</span> </label>
                                                        <input class="form-control" name="cred_out" id="cred_out"></input>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Submit</button>
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
@endsection
