
@extends('admin.layout.master')
@section('title', 'Edit Sub Category')
@section('content')
 
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Sub Category</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical"  method="post" action="{{ route('sub-category.update',encrypt($subCategory->id)) }}" id="add_sub_category" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="name">Name</label>
                                                <input type="text" id="name" name="name" class="form-control" class="form-control"
                                                     placeholder="Name" value="{{($subCategory->name) ? $subCategory->name : ''}}">
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="category_id">Category</label>
                                                <select name="category_id" id="category_id" class="form-control">
                                                    <option value="">Select Category</option>
                                                    @foreach ($category as $value)
                                                        <option value="{{$value->id}}" {{($subCategory->category_id == $value->id) ? 'selected' : ''}}>{{$value->name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label"    for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="">Select Status</option>
                                                    <option value="1" {{($subCategory->status == 1) ? 'selected' : ''}}>Active</option>
                                                    <option value="2" {{($subCategory->status == 2) ? 'selected' : ''}}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="image">Upload File</label>
                                                <input type="file" id="image" name="image" class="form-control" class="form-control">
                                                @error('image')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex mt-3">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                            <a href="{{route('sub-category.index')}}">
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
        $("#add_sub_category").validate({
            rules: {
                name: {
                    required: true, 
                },
                category_id: {
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

