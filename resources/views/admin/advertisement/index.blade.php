
@extends('admin.layout.master')
@section('title', 'Advertisement')
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class = "col-md-12">
                        <h5 class="card-title">Advertisement</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap p-2" id="advertisement">
        
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        var qstring = '';
        $(document).ready(function(){
            getAdvertisementData(qstring);
        });

        function getAdvertisementData(qstring) {
            $.ajax({
                url: "{{ route('advertisement.index')}}?"+qstring,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    $('#advertisement').html(data.advertisementData);
                    $('#advertisementTable').DataTable({
                        aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    });
                }
            }).fail(function() {
            });
        }

        $(document).on('click','#approved',function(){
            var avd_id = $(this).data('id')
            swal({
                title: "Are you sure?",
                text: "You want to unapprove this advertisent!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    approvedAdvertisement(avd_id)
                } 
            });
        });

        $(document).on('click','#unapproved',function(){
            var avd_id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "You want to approve this advertisent!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    approvedAdvertisement(avd_id)
                } 
            });
        });

        function approvedAdvertisement(avd_id){
            var url = '{{ route("advertisement.approve", ":id") }}';
            url = url.replace(':id', avd_id);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : "PUT",
                url: url,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    swal("Your status has been changed!", {
                        icon: "success",
                    });
                    getAdvertisementData(qstring);
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

