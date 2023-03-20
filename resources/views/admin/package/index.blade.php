
@extends('admin.layout.master')
@section('title', 'Package')
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class = "col-md-6">
                        <h5 class="card-title">Package</h5>
                    </div>
                    <div class = "col-md-6 text-end">
                        <a href="{{route('package.create')}}">
                            <button type="button" class="btn btn-primary">Add Package</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap p-2" id="package">
        
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        var qstring = '';
        $(document).ready(function(){
            getPackageData(qstring);
        });

        function getPackageData(qstring) {
            $.ajax({
                url: "{{ route('package.index')}}?"+qstring,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    $('#package').html(data.packageData);
                    $('#packageTable').DataTable({
                        aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    });
                }
            }).fail(function() {
            });
        }

        $(document).on('click','#deletePackage',function(){
            var packageId = $(this).data('id')
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this package!",
                type: 'warning',
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    deletePackage(packageId)
                } 
            });
        });

        function deletePackage(packageId){
            var url = '{{ route("package.destroy", ":id") }}';
            url = url.replace(':id', packageId);
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
                        html: 'Package has been deleted!',
                    });
                    getPackageData(qstring);
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

