@extends('Backend.layouts.master')
@section('title')
    Map Product Price
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
                                        <h1 class="mb-3">Map Product Price</h1>
                                        <form action="{{ Route('map_product_price.update',$map_product_price->id) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label>Distributor<span class="danger"> *</span></label>
                                                           <select class="form-control" name="distributor" id="">
                                                            <option value="">--------- Please Select Distributor----------</option>
                                                            @foreach ($distributors as $distri)
                                                                <option value="{{ $distri->id }}" {{ $distri->id == $map_product_price->user_id ? 'selected' : '' }}>
                                                                    {{ $distri->name }}
                                                                </option>
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label>Product<span class="danger"> *</span></label>
                                                            <select class="form-control" name="product" id="">
                                                                <option value="">--------- Please Select Product----------</option>
                                                                @foreach ($products as $pro)
                                                                <option value="{{$pro->id}}"{{$pro->id==$map_product_price->product_id ? 'selected':''}}>{{$pro->product_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label>Price<span class="danger"> *</span></label>
                                                            <input type="number" class="form-control"
                                                                placeholder="Price" value="{{$map_product_price->price}}" name="price" required
                                                                data-validation-required-message="This Price field is required">
                                                        </div>
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
@endsection
