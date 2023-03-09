<table class="table table-hover" id="categoryTable">
    <thead>
      <tr>
        <th>Id</th>
        <th>Image</th>
        <th>Name</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        @forelse($category as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><img src="{{$row->getFirstMediaUrl('category', 'thumb')}}"  width="120px"></td>
                <td>{{!empty($row->name) ? $row->name : '-'}}</td>
                @if ($row->status == 1)
                <td>
                    <span class="badge bg-success">Active</span>
                </td>
                @else
                  <td><span class="badge bg-danger">Inactive</span></td>
                @endif
                <td>
                    <a href="{{route('category.edit',encrypt($row->id))}}">
                        <i class="fa fa-pencil btn btn-primary" aria-hidden="true"></i>
                    </a>
                    <a href="javascript:void(0)" data-id="{{encrypt($row->id)}}" id="deleteCategory">
                        <i class="fa fa-trash btn btn-danger" aria-hidden="true"></i>
                    </a>
                </td>               
            </tr>
        @empty
            <td colspan='10' class="text-center">No records available</td>
        @endforelse
    </tbody>
  </table>
