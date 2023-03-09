
@extends('admin.layout.master')
@section('title', 'Add Country')
@section('content')
 
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Country</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical"  method="post" action="{{ route('country.store') }}" id="add_country">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="name">Name</label>
                                                <input type="text" id="name" name="name" type="text" class="form-control" class="form-control"
                                                    name="name" placeholder="Name" value="{{old('name')}}">
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="short_name">Short Name</label>
                                                <input type="text" id="short_name" name="short_name" type="text" class="form-control" class="form-control"
                                                    placeholder="Short Name" value="{{old('short_name')}}">
                                                @error('short_name')
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
                                                    <option value="2">Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex mt-3">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                            <a href="{{route('country.index')}}">
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
        $("#add_country").validate({
            rules: {
                name: {
                    required: true, 
                },
                short_name:{
                    required: true, 
                },
                status: {
                    required: true, 
                }
            },
        })
    });
</script>
@endpush

