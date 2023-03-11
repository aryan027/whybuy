
@extends('admin.layout.master')
@section('title', 'User')
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class = "col-md-6">
                        <h5 class="card-title">Users</h5>
                    </div>
                    {{-- <div class = "col-md-6 text-end">
                        <a href="{{route('user.create')}}">
                            <button type="button" class="btn btn-primary">Add User</button>
                        </a>
                    </div> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap p-2" id="user">
        
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        var qstring = '';
        $(document).ready(function(){
            getUserData(qstring);
        });

        function getUserData(qstring) {
            $.ajax({
                url: "{{ route('user.index')}}?"+qstring,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    $('#user').html(data.userData);
                    $('#userTable').DataTable({
                        aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    });
                }
            }).fail(function() {
            });
        }

        $(document).on('click','#active',function(){
            var user_id = $(this).data('id')
            swal({
                title: "Are you sure?",
                text: "You want to inactive this user!",
                type: 'warning',
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    activeUserStatus(user_id)
                } 
            });
        });

        $(document).on('click','#inactive',function(){
            var user_id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "You want to active this user!",
                type: 'warning',
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    activeUserStatus(user_id)
                } 
            });
        });

        function activeUserStatus(user_id){
            var url = '{{ route("user.status", ":id") }}';
            url = url.replace(':id', user_id);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : "PUT",
                url: url,
                dataType: 'json',
            }).done(function(data) {
                if(data.status == 1){
                    swal({
                        type: 'success',
                        html: 'Your status has been changed!',
                    });
                    getUserData(qstring);
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

