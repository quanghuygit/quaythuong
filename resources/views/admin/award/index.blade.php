@extends('layout.admin.index')

@section('main-content')
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading clearfix">
                <h4 class="panel-title">Danh sách bốc thăm</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="example" class="display table" style="width: 100%; cellspacing: 0;">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên giải thưởng</th>
                                <th>Số người trúng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>STT</th>
                                <th>Tên giải thưởng</th>
                                <th>Số người trúng</th>
                                <th>Thao tác</th>
                            </tr>
                        </tfoot>
                        <tbody>
                        @if(!empty($awards))
                            @foreach($awards as $award)
                                <tr>
                                    <td>{{ $award->id }}</td>
                                    <td>{{ $award->name }}</td>
                                    <td>{{ $award->number }}</td>
                                    <td>
                                        <a href="{{ route('award.delete', $award->id) }}" class="btn btn-danger">Xóa</a>
                                        <a href="{{ route('award.edit', $award->id) }}" class="btn btn-info">Sửa</a>
                                        <a href="{{ route('award.createWinner', $award->id) }}" class="btn btn-info">Thêm người trúng giải</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <link href="{{ asset('admin/plugins/datatables/css/jquery.datatables.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/plugins/datatables/css/jquery.datatables_themeroller.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/plugins/bootstrap-datepicker/css/datepicker3.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('admin/plugins/datatables/js/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').dataTable({
                language: {
                    "decimal": "",
                    "emptyTable": "Không có khách hàng nào trong danh sách bốc thăm",
                    "info": "Hiển thị từ _START_ đến _END_ trong tổng _TOTAL_ khách hàng",
                    "infoEmpty": "Hiển thị từ 0 đến 0 trong tổng 0 khách hàng",
                    "infoFiltered": "(Đã lọc được _MAX_ khách hàng)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Hiển thị _MENU_ khách hàng",
                    "loadingRecords": "Đang tìm kiếm...",
                    "processing": "Đang xử lý...",
                    "search": "Tìm kiếm:",
                    "zeroRecords": "Không có khách hàng nào thỏa mãn điều kiện tìm kiếm",
                    "paginate": {
                        "first": "Trang đầu",
                        "last": "Trang cuối",
                        "next": "Trang tiếp ",
                        "previous": "Trang trước"
                    },
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    }
                }
            });
        });
    </script>
@endpush