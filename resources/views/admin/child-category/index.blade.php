
@extends('admin.layout.master')
@section('title', 'Child Category')
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class = "col-md-6">
                        <h5 class="card-title">Child Category</h5>
                    </div>
                    <div class = "col-md-6 text-end">
                        <a href="{{route('child-category.create')}}">
                            <button type="button" class="btn btn-primary">Add Child Category</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap p-2" id="child_category">
        
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        var qstring = '';
        $(document).ready(function(){
            getChildCategoryData(qstring);
        });

        function getChildCategoryData(qstring) {
            $.ajax({
                url: "{{ route('child-category.index')}}?"+qstring,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    $('#child_category').html(data.childCategoryData);
                    $('#childCategoryTable').DataTable({
                        aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    });
                }
            }).fail(function() {
            });
        }

        $(document).on('click','#deleteChildCategory',function(){
            var categoryId = $(this).data('id')
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this child category!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    deleteChildCategory(categoryId)
                } 
            });
        });

        function deleteChildCategory(categoryId){
            var url = '{{ route("child-category.destroy", ":id") }}';
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
                    swal("Your child category has been deleted!", {
                        icon: "success",
                    });
                    getChildCategoryData(qstring);
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

