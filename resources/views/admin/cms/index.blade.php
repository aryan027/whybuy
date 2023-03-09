
@extends('admin.layout.master')
@section('title', 'CMS')
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class = "col-md-6">
                        <h5 class="card-title">CMS</h5>
                    </div>
                    <div class = "col-md-6 text-end">
                        <a href="{{route('cms.create')}}">
                            <button type="button" class="btn btn-primary">Add CMS</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap p-2" id="cms">
        
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        var qstring = '';
        $(document).ready(function(){
            getCMSData(qstring);
        });

        function getCMSData(qstring) {
            $.ajax({
                url: "{{ route('cms.index')}}?"+qstring,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    $('#cms').html(data.cmsData);
                    $('#cmsTable').DataTable({
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
                    getCMSData(qstring);
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

