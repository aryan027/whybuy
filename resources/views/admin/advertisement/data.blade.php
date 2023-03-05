<table class="table table-hover" id="advertisementTable">
    <thead>
      <tr>
        <th>Id</th>
        <th>User</th>
        <th>Category</th>
        <th>Sub Category</th>
        <th>Title</th>
        <th>Brand</th>
        <th>Status</th>
        <th>Approved Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        @forelse($advertisement as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{!empty($row->getUser) ? $row->getUser->full_name : '-'}}</td>
                <td>{{!empty($row->getCategory) ? $row->getCategory->name : '-'}}</td>
                <td>{{!empty($row->getSubCategory) ? $row->getSubCategory->name : '-'}}</td>
                <td>{{!empty($row->title) ? $row->title : '-'}}</td>
                <td>{{!empty($row->brand) ? $row->brand : '-'}}</td>
                @if ($row->status == 1)
                <td>
                    <span class="badge bg-success">Active</span>
                </td>
                @else
                  <td><span class="badge bg-danger">Inactive</span></td>
                @endif
                @if ($row->approved == 1)
                <td>
                    <span class="badge bg-success approved-status" data-id="{{encrypt($row->id)}}" id="approved">Approved</span>
                </td>
                @else
                  <td><span class="badge bg-warning approved-status" data-id="{{encrypt($row->id)}}" id="unapproved">Unapproved</span></td>
                @endif
                <td>
                    <a href="{{route('advertisement.show',encrypt($row->id))}}">
                        <i class="fa fa-eye btn btn-primary" aria-hidden="true"></i>
                    </a>
                </td>               
            </tr>
        @empty
            <td colspan='10' class="text-center">No records available</td>
        @endforelse
    </tbody>
  </table>
