
@extends('admin.layout.master')
@section('title', 'Sub Category')
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class = "col-md-6">
                        <h5 class="card-title">Sub Category</h5>
                    </div>
                    <div class = "col-md-6 text-end">
                        <a href="{{route('sub-category.create')}}">
                            <button type="button" class="btn btn-primary">Add Sub Category</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap p-2" id="sub_category">
        
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        var qstring = '';
        $(document).ready(function(){
            getSubCategoryData(qstring);
        });

        function getSubCategoryData(qstring) {
            $.ajax({
                url: "{{ route('sub-category.index')}}?"+qstring,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    $('#sub_category').html(data.subCategoryData);
                    $('#subCategoryTable').DataTable({
                        aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    });
                }
            }).fail(function() {
            });
        }

        $(document).on('click','#deleteSubCategory',function(){
            var categoryId = $(this).data('id')
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this sub category!",
                type: 'warning',
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    deleteSubCategory(categoryId)
                } 
            });
        });

        function deleteSubCategory(categoryId){
            var url = '{{ route("sub-category.destroy", ":id") }}';
            url = url.replace(':id', categoryId);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : "DELETE",
                url: url,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    swal({
                        type: 'success',
                        html: 'Sub category has been deleted!',
                    });
                    getSubCategoryData(qstring);
                }else if(data.status == 2){
                    window.location.reload();
                }else{
                    swal({
                        type: 'error',
                        html: 'Something went to wrong!',
                    });
                }
            }).fail(function() {
            });
        }

    </script>
@endpush

