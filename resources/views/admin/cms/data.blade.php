<table class="table table-hover" id="cmsTable">
    <thead>
      <tr>
        <th>Id</th>
        <th>Type</th>
        <th>Description</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        @forelse($cms as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{!empty($row->type) ? $row->type : '-'}}</td>
                <td>{!! !empty($row->description) ? $row->description : '' !!}</td>
                <td>
                    <a href="{{route('cms.edit',encrypt($row->id))}}">
                        <i class="fa fa-pencil btn btn-primary" aria-hidden="true"></i>
                    </a>
                </td>               
            </tr>
        @empty
            <td colspan='10' class="text-center">No records available</td>
        @endforelse
    </tbody>
  </table>
