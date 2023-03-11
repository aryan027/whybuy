
@extends('admin.layout.master')
@section('title', 'Edit User')
@section('content')
 
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit User</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical"  method="post" action="{{ route('user.update',encrypt($user->id)) }}" id="update_user" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="fname">First Name</label>
                                                <input type="text" id="fname" name="fname" class="form-control"
                                                placeholder="First name" value="{{($user->fname) ?? $user->fname}}">
                                                @error('fname')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="lname">Last Name</label>
                                                <input type="text" id="lname" name="lname" class="form-control"
                                                placeholder="Last name" value="{{($user->lname) ?? $user->lname}}">
                                                @error('lname')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="email">Email</label>
                                                <input type="text" id="email" name="email" class="form-control"
                                                placeholder="Email" value="{{($user->email) ?? $user->email}}">
                                                @error('email')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="mobile">mobile</label>
                                                <input type="number" id="mobile" name="mobile" class="form-control"
                                                placeholder="Mobile number" value="{{($user->mobile) ?? $user->mobile}}">
                                                @error('mobile')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="gender">Gender</label>
                                                <div class="">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="gender" id="male" value="male" {{($user->gender == 'male') ? 'checked' : ''}} >
                                                        <label class="form-check-label" for="male">Male</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="gender" id="female" value="female"  {{($user->gender == 'female')  ? 'checked' : ''}}>
                                                        <label class="form-check-label" for="female">Female</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="dob">DOB</label>
                                                <input type="date" id="dob" name="dob" class="form-control"
                                                value="{{($user->dob) ?? $user->dob}}">
                                                @error('dob')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="">Select Status</option>
                                                    <option value="1" {{($user->status == 1) ? 'selected' : ''}}>Active</option>
                                                    <option value="0" {{($user->status == 0) ? 'selected' : ''}}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="form-label" for="profile_picture">Profile Picture</label>
                                                <input type="file" id="profile_picture" name="profile_picture" class="form-control" class="form-control">
                                                @error('profile_picture')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex mt-3">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                            <a href="{{route('category.index')}}">
                                                <button type="button" class="btn btn-secondary me-1 mb-1">Cancel</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- // Basic Vertical form layout section end -->
@endsection
@push('js')
<script>
    $(document).ready(function(){
        $("#update_user").validate({
            rules: {
                fname: {
                    required: true, 
                },
                lname: {
                    required: true, 
                },
                email: {
                    required: true,
                    email: true 
                },
                mobile: {
                    required: true, 
                    minlength: 10,
                    maxlength: 10
                },
                dob: {
                    required: true, 
                },
                status: {
                    required: true, 
                },
                password: {
                    required: true,
                    minlength: 5
                },
                confirm_password: {
                    required: true,
                    minlength: 5,
                    equalTo: '[name="password"]'
                },
            },
        })
    });
</script>
@endpush

