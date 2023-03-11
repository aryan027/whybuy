<table class="table table-hover" id="userTable">
    <thead>
      <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Mobile No.</th>
        <th>Gender</th>
        <th>Status</th>
        {{-- <th>Action</th> --}}
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        @forelse($user as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{!empty($row->full_name) ? $row->full_name : '-'}}</td>
                <td>{{!empty($row->mobile) ? $row->mobile : '-'}}</td>
                <td>{{!empty($row->gender) ? $row->gender : '-'}}</td>
                @if ($row->status == 1)
                <td>
                    <span class="badge bg-success pointer-status" data-id="{{encrypt($row->id)}}" id="active">Active</span>
                </td>
                @else
                  <td><span class="badge bg-danger pointer-status" data-id="{{encrypt($row->id)}}" id="inactive">Inactive</span></td>
                @endif
                {{-- <td>
                    <a href="{{route('user.edit',encrypt($row->id))}}">
                        <i class="fa fa-pencil btn btn-primary" aria-hidden="true"></i>
                    </a>
                </td>              --}}
            </tr>
        @empty
            <td colspan='10' class="text-center">No records available</td>
        @endforelse
    </tbody>
  </table>
