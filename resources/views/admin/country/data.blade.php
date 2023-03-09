<table class="table table-hover" id="countryTable">
    <thead>
      <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Short Name</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        @forelse($categories as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{!empty($row->name) ? $row->name : '-'}}</td>
                <td>{{!empty($row->short_name) ? $row->short_name : '-'}}</td>
                @if ($row->status == 1)
                <td>
                    <span class="badge bg-success">Active</span>
                </td>
                @else
                  <td><span class="badge bg-danger">Inactive</span></td>
                @endif
                <td>
                    <a href="{{route('country.edit',encrypt($row->id))}}">
                        <i class="fa fa-pencil btn btn-primary" aria-hidden="true"></i>
                    </a>
                    <a href="javascript:void(0)" data-id="{{encrypt($row->id)}}" id="deleteCountry">
                        <i class="fa fa-trash btn btn-danger" aria-hidden="true"></i>
                    </a>
                </td>               
            </tr>
        @empty
            <td colspan='10' class="text-center">No records available</td>
        @endforelse
    </tbody>
  </table>
