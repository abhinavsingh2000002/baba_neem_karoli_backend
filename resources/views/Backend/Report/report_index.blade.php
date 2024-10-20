@extends('Backend.layouts.master')
@section('title')
Order Report
@endsection
@section('page-content')
  <!-- BEGIN: Content-->
  <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- users list start -->
            <section class="users-list-wrapper">
                <div class="users-list-filter px-1">
                    <form>
                        <div class="row border border-light rounded py-2 mb-2">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="users-list-verified">Date</label>
                                <fieldset class="form-group">
                                    <input type="date" class="form-control" id="date">
                                </fieldset>
                            </div>
                            {{-- <div class="col-12 col-sm-6 col-lg-3">
                                <label for="users-list-role">Role</label>
                                <fieldset class="form-group">
                                    <select class="form-control" id="users-list-role">
                                        <option value="">Any</option>
                                        <option value="User">User</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                </fieldset>
                            </div> --}}
                            {{-- <div class="col-12 col-sm-6 col-lg-3">
                                <label for="users-list-status">Status</label>
                                <fieldset class="form-group">
                                    <select class="form-control" id="users-list-status">
                                        <option value="">Any</option>
                                        <option value="Active">Active</option>
                                        <option value="Close">Close</option>
                                        <option value="Banned">Banned</option>
                                    </select>
                                </fieldset>
                            </div> --}}
                            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                                <button id="search" class="btn btn-block btn-primary glow">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
            <!-- users list ends -->
        </div>
    </div>
</div>
<!-- END: Content-->
@endsection
@section('page-js')
<script>
    $(document).ready(function() {
        $('#search').on('click', function(e) {
            e.preventDefault();
            searchOrder();
        });
    });

    function searchOrder() {
        var date=$('#date').val();
        $.ajax({
            url:"{{Route('admin_order_report.listing')}}",
            type:"GET",
            data:{
                date:date,
            },
            success:function(response){

            },
            error:function(xhr,status,error){
                console.log('Error',error);
            },
        })
    }
    </script>
@endsection
