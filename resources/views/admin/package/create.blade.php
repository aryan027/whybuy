
@extends('admin.layout.master')
@section('title', 'Add Package')
@section('content')
 
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Package</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical"  method="post" action="{{ route('package.store') }}" id="add_package" enctype="multipart/form-data">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="package_name">Name</label>
                                                <input type="text" id="package_name" name="package_name" class="form-control"
                                                placeholder="Package name" value="{{old('package_name')}}">
                                                @error('package_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="package_price">Price</label>
                                                <input type="number" id="package_price" name="package_price" class="form-control"
                                                placeholder="Package price" value="{{old('package_price')}}">
                                                @error('package_price')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="no_of_ads">Number Of Ads</label>
                                                <input type="number" id="no_of_ads" name="no_of_ads" class="form-control"
                                                placeholder="Number of ads" value="{{old('no_of_ads')}}">
                                                @error('no_of_ads')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="durations">Durations (In Day)</label>
                                                <input type="number" id="durations" name="durations" class="form-control"
                                                placeholder="Duration" value="{{old('durations')}}">
                                                @error('durations')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="type">Type</label>
                                                <select name="type" id="type" class="form-control">
                                                    <option value="">Select type</option>
                                                    <option value="free">Free</option>
                                                    <option value="paid">Paid</option>
                                                </select>
                                                @error('type')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="">Select Status</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="type">Description</label>
                                                <textarea type="text" class="form-control" id="editor" name="description" placeholder="Enter content description here">{{old('description')}}</textarea>
                                                @error('description')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>     
                                        
                                      
                                        <div class="col-12 d-flex mt-3">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                            <a href="{{route('package.index')}}">
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
        $("#add_package").validate({
            rules: {
                package_name: {
                    required: true, 
                },
                package_price: {
                    required: true, 
                    maxlength: 8
                },
                no_of_ads: {
                    required: true,
                    maxlength: 3
                },
                durations: {
                    required: true, 
                    maxlength: 3
                },
                dob: {
                    required: true, 
                },
                type: {
                    required: true,
                },
                status: {
                    required: true, 
                },
               
            },
        })
    });
</script>
@endpush

