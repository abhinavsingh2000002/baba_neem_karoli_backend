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
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Get current date
                                    const today = new Date();
                                    const yyyy = today.getFullYear();
                                    const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                                    const dd = String(today.getDate()).padStart(2, '0');

                                    // Set the input value to the current date in YYYY-MM-DD format
                                    document.getElementById('date').value = `${yyyy}-${mm}-${dd}`;
                                });
                            </script>
                            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                                <button id="search" class="btn btn-block btn-primary glow">Search</button>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                                <button id="pdf_download" class="btn btn-block btn-success glow">PDF Download</button>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                                <button id="excel_download" class="btn btn-block btn-danger glow">Excel Download</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="report"></div>
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
        if(date){
            $.ajax({
                url:"{{Route('driver_report.listing')}}",
                type:"GET",
                data:{
                    date:date,
                },
                success:function(response){
                    if (response.failed == 'No Order Found !') {
                            alert("No Order Found");
                    } else {
                    $('#report').html(response);
                    }
                },
                error:function(xhr,status,error){
                    console.log('Error',error);
                },
            })
        }
        else{
            alert("Please Select Data First !")
        }
    }
    $(document).ready(function() {
        $('#pdf_download').on('click', function(e) {
            e.preventDefault();
            reportpdf();
        });
    });


    function reportpdf()
    {
        var date=$('#date').val();
        if(date){
            $.ajax({
                url:"{{Route('driver_report.reportpdf')}}",
                type:"GET",
                data:{
                    date:date,
                },
                success:function(response){
                    if (response.failed == 'No Order Found !') {
                            alert("No Order Found");
                    } else {
                    window.location.href ="{{Route('driver_report.reportpdf')}}" + "?date="+date;
                    }
                },
                error:function(xhr,status,error){
                    console.log('Error',error);
                },
            })
        }
        else{
            alert('Please Select the Date Filter');
        }
    }

    $(document).ready(function() {
        $('#excel_download').on('click', function(e) {
            e.preventDefault();
            reportExcel();
        });
    });


    function reportExcel()
    {
        var date=$('#date').val();
        if(date){
            $.ajax({
                url:"{{Route('driver_report.reportExcel')}}",
                type:"GET",
                data:{
                    date:date,
                },
                success:function(response){
                    window.location.href ="{{Route('driver_report.reportExcel')}}" + "?date="+date;
                },
                error:function(xhr,status,error){
                    console.log('Error',error);
                },
            })
        }
        else{
            alert('Please Select the Date Filter');
        }
    }
    </script>
@endsection
