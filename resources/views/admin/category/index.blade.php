
@extends('admin.layout.master')
@section('title', 'Category')
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class = "col-md-6">
                        <h5 class="card-title">Category</h5>
                    </div>
                    <div class = "col-md-6 text-end">
                        <a href="{{route('category.create')}}">
                            <button type="button" class="btn btn-primary">Add Category</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap p-2" id="category">
        
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        var qstring = '';
        $(document).ready(function(){
            getCategoryData(qstring);
        });

        function getCategoryData(qstring) {
            $.ajax({
                url: "{{ route('category.index')}}?"+qstring,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    $('#category').html(data.categoryData);
                    $('#categoryTable').DataTable({
                        aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    });
                }
            }).fail(function() {
            });
        }

        $(document).on('click','#deleteCategory',function(){
            var categoryId = $(this).data('id')
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this category!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    deleteCategory(categoryId)
                } 
            });
        });

        function deleteCategory(categoryId){
            var url = '{{ route("category.destroy", ":id") }}';
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
                    swal("Your category has been deleted!", {
                        icon: "success",
                    });
                    getCategoryData(qstring);
                }else if(data.status == 2){
                    window.location.reload();
                }else{
                    swal("Something went to wrong!", {
                        icon: "error",
                    });
                }
            }).fail(function() {
            });
        }

    </script>
@endpush

