<table class="table table-hover" id="packageTable">
    <thead>
      <tr>
        <th>Id</th>
        <th>Package Name</th>
        <th>Price</th>
        <th>Number of Ads</th>
        <th>Duration</th>
        <th>Type</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        @forelse($package as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{!empty($row->package_name) ? $row->package_name : '-'}}</td>
                <td>{{!empty($row->package_price) ? $row->package_price : '-'}}</td>
                <td>{{!empty($row->no_of_ads) ? $row->no_of_ads : '-'}}</td>
                <td>{{!empty($row->durations) ? $row->durations.' Days' : '-'}}</td>
                <td>{{!empty($row->type) ? $row->type : '-'}}</td>
                @if ($row->status == 1)
                <td>
                    <span class="badge bg-success pointer-status" data-id="{{encrypt($row->id)}}" id="active">Active</span>
                </td>
                @else
                  <td><span class="badge bg-danger pointer-status" data-id="{{encrypt($row->id)}}" id="inactive">Inactive</span></td>
                @endif
                <td>
                    <a href="{{route('package.edit',encrypt($row->id))}}">
                        <i class="fa fa-pencil btn btn-primary" aria-hidden="true"></i>
                    </a>
                    <a href="javascript:void(0)" data-id="{{encrypt($row->id)}}" id="deletePackage">
                        <i class="fa fa-trash btn btn-danger" aria-hidden="true"></i>
                    </a>
                </td>             
            </tr>
        @empty
            <td colspan='10' class="text-center">No records available</td>
        @endforelse
    </tbody>
  </table>
