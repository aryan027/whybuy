
@extends('admin.layout.master')
@section('title', 'Add CMS')
@section('content')
 
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add CMS</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical"  method="post" action="{{ route('cms.store') }}" id="add_cms">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="type">Type</label>
                                                <select name="type" id="type" class="form-control">
                                                    <option value="">Select Type</option>
                                                    <option value="terms_condition">Terms and condition</option>
                                                    <option value="privacy_policy">Privacy Policy</option>
                                                    <option value="abount_us">About Us</option>
                                                </select>
                                                @error('type')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="type">Description</label>
                                                <textarea type="text" class="form-control" id="editor" name="description" placeholder="Enter content description here">{{old('description')}}</textarea>
                                                @error('description')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>                                         
                                        
                                        <div class="col-12 d-flex mt-3">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                            <a href="{{route('cms.index')}}">
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
        $("#add_cms").validate({
            rules: {
                type: {
                    required: true, 
                },
                description: {
                    required: true, 
                }
            },
        })
    });
</script>
@endpush

