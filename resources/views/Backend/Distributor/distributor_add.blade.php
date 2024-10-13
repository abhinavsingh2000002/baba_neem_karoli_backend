@extends('Backend.layouts.master')
@section('title')
Add Distributor
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
                                    <form action="{{Route('distributor.create')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="media mb-2">
                                            <a class="mr-2" href="#">
                                                <img id="profile-img" src="../../../app-assets/images/portrait/small/avatar-s-27.png" alt="user's avatar" class="users-avatar-shadow rounded-circle" height="64" width="64">
                                            </a>
                                            <div class="media-body">
                                                <h4 class="media-heading">Profile Image</h4>
                                                <p class="text-muted">Please upload a JPEG or PNG file (max size: 10MB).</p>
                                                <div class="col-12 px-0 d-flex">
                                                    <label for="file-upload" class="btn btn-sm btn-primary mr-25">Change</label>
                                                    <input type="file" id="file-upload" name="image_path" accept=".jpeg,.jpg,.png" style="display: none;" required />
                                                </div>
                                                <small id="file-error" class="text-danger" style="display: none;">Invalid file format or size. Please upload a JPEG or PNG file under 10MB.</small>
                                            </div>
                                        </div>
                                        <!-- users edit media object ends -->
                                        <!-- users edit account form start -->

                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Full Name <span class="danger"> *</span></label>
                                                        <input type="text" class="form-control" placeholder="Full Name" value="" name="name" required data-validation-required-message="This full name field is required">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Email <span class="danger">*</span></label>
                                                        <input type="email" class="form-control" placeholder="Email" value="" name="email" required data-validation-required-message="This email field is required">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Contact Number <span class="danger"> *</span></label>
                                                        <input type="number" class="form-control" placeholder="contact Number" value="" name="mobile" required data-validation-required-message="This Contact No field is required">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>DOB <span class="danger"> *</span></label>
                                                        <input type="date" class="form-control" placeholder="DOB" value="" name="dob" required data-validation-required-message="This DOB field is required">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Role <span class="danger"> *</span></label>
                                                    <select class="form-control" name="role_id">
                                                        <option value="">-------Please select Role------</option>
                                                        @foreach ($role as $data )
                                                        <option value="{{$data->id}}">{{$data->role_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Aadhar Number <span class="danger"> *</span></label>
                                                        <input type="number" class="form-control" placeholder="Aadhar Number" value="" name="aadhar_number" required data-validation-required-message="This Aadhar Number field is required">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Pan Number<span class="danger"> *</span></label>
                                                        <input type="text" class="form-control" placeholder="Pan Number" value="" name="pan_number" required data-validation-required-message="This Pan Number field is required">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Password <span class="danger"> *</span></label>
                                                    <input type="password" class="form-control" placeholder="Password" name="password" required data-validation-required-message="This password field is required">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="address"> Address <span class="danger"> *</span> </label>
                                                        <textarea class="form-control" name="address" id="address" rows="3"></textarea>
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
<script>
    document.getElementById('file-upload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const validTypes = ['image/jpeg', 'image/png'];
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        const errorMessage = document.getElementById('file-error');
        const imgPreview = document.getElementById('profile-img');

        if (file) {
            if (!validTypes.includes(file.type) || file.size > maxSize) {
                errorMessage.style.display = 'block';
                event.target.value = ''; // Clear the input
                imgPreview.src = "../../../app-assets/images/portrait/small/avatar-s-26.png"; // Reset to default image
            } else {
                errorMessage.style.display = 'none';

                // Preview the image by creating a URL for the selected file
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result; // Set image source to the selected file
                };
                reader.readAsDataURL(file);
            }
        }
    });
</script>
@endsection
