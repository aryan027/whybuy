
@extends('admin.layout.master')
@section('title', 'Edit Child Category')
@section('content')
 
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Child Category</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical"  method="post" action="{{ route('child-category.update',encrypt($childCategory->id)) }}" id="add_child_category">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="name">Name</label>
                                                <input type="text" id="name" name="name" type="text" class="form-control" class="form-control"
                                                     placeholder="Name" value="{{($childCategory->name) ? $childCategory->name : ''}}">
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="sub_category_id">Category</label>
                                                <select name="sub_category_id" id="sub_category_id" class="form-control">
                                                    <option value="">Select Category</option>
                                                    @foreach ($subCategory as $value)
                                                        <option value="{{$value->id}}" {{($childCategory->sub_category_id == $value->id) ? 'selected' : ''}}>{{$value->name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('sub_category_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-6">
                                            <div class="form-group mandatory">
                                                <label class="form-label"    for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="">Select Status</option>
                                                    <option value="1" {{($childCategory->status == 1) ? 'selected' : ''}}>Active</option>
                                                    <option value="2" {{($childCategory->status == 2) ? 'selected' : ''}}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex mt-3">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                            <a href="{{route('child-category.index')}}">
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
        $("#add_child_category").validate({
            rules: {
                name: {
                    required: true, 
                },
                sub_category_id: {
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

