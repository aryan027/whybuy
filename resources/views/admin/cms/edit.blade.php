
@extends('admin.layout.master')
@section('title', 'Edit CMS')
@section('content')
 
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit CMS</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical"  method="post" action="{{ route('cms.update',encrypt($cms->id)) }}" id="add_category">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="type">Type</label>
                                                <select name="type" id="type" class="form-control">
                                                    <option value="">Select Type</option>
                                                    <option value="terms_condition" {{($cms->type == 'terms_condition') ? 'selected' : ''}}>Terms and condition</option>
                                                    <option value="privacy_policy" {{($cms->type == 'privacy_policy') ? 'selected' : ''}}>Privacy Policy</option>
                                                    <option value="abount_us" {{($cms->type == 'abount_us') ? 'selected' : ''}}>About Us</option>
                                                </select>
                                                @error('type')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mandatory">
                                                <label class="form-label" for="type">Description</label>
                                                <textarea type="text" class="form-control" id="editor" name="description" placeholder="Enter content description here">
                                                    {!! !empty($cms->description) ? $cms->description : '' !!}
                                                </textarea>
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
        $("#add_category").validate({
            rules: {
                name: {
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

