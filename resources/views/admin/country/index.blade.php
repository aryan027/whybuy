
@extends('admin.layout.master')
@section('title', 'Country')
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class = "col-md-6">
                        <h5 class="card-title">Country</h5>
                    </div>
                    <div class = "col-md-6 text-end">
                        <a href="{{route('country.create')}}">
                            <button type="button" class="btn btn-primary">Add Country</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap p-2" id="country">
        
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        var qstring = '';
        $(document).ready(function(){
            getCountryData(qstring);
        });

        function getCountryData(qstring) {
            $.ajax({
                url: "{{ route('country.index')}}?"+qstring,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    $('#country').html(data.categoriesData);
                    $('#countryTable').DataTable({
                        aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    });
                }
            }).fail(function() {
            });
        }

        $(document).on('click','#deleteCountry',function(){
            var countryId = $(this).data('id')
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this country!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    deleteCountry(countryId)
                } 
            });
        });

        function deleteCountry(countryId){
            var url = '{{ route("country.destroy", ":id") }}';
            url = url.replace(':id', countryId);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : "DELETE",
                url: url,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    swal("Your country has been deleted!", {
                        icon: "success",
                    });
                    getCountryData(qstring);
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

