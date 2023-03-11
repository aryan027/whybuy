
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
            // swal({
            //     title: "Are you sure?",
            //     text: "You want to unapprove this advertisent!",
            //     icon: "warning",
            //     buttons: true,
            //     dangerMode: true,
            // })
            // .then((willDelete) => {
            //     if (willDelete) {
            //         approvedAdvertisement(avd_id)
            //     } 
            // });
            swal({
                title: "Are you sure?",
                text: "You want to unapprove this advertisent!",
                input: 'select',
                inputOptions: {
                    'Unathorized User': 'Unathorized User',
                    'Not a valid user': 'Not a valid user',
                    'Not a valid mobile number': 'Not a valid mobile number'
                },
                inputPlaceholder: 'Select reason',
                showCancelButton: true,
                inputValidator: function (value) {
                    return new Promise(function (resolve, reject) {
                    if (value != '') {
                        approvedAdvertisement(avd_id,value)
                    } else {
                        reject('You need to select reason :)')
                    }
                    })
                }
            });
        });

        $(document).on('click','#unapproved',function(){
            var avd_id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "You want to approve this advertisent!",
                type: 'warning',
                buttons: true,
                dangerMode: true,
                showCancelButton: true,

            })
            .then((willDelete) => {
                if (willDelete) {
                    approvedAdvertisement(avd_id,'')
                } 
            });
        });

        function approvedAdvertisement(avd_id,reason){
            var url = '{{ route("advertisement.approve", ":id") }}';
            url = url.replace(':id', avd_id);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : "PUT",
                url: url,
                data:{reason:reason},
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    swal({
                        type: 'success',
                        html: 'Your status has been changed!',
                    });
                    getAdvertisementData(qstring);
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

