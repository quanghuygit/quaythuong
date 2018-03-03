@extends('layout.admin.index')

@section('main-content')
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading clearfix">
                <h4 class="panel-title">Nhập danh sách rút thăm</h4>
            </div>
            <div class="panel-body">
                <form class="form-inline" method="post" action="{{ route('contract.store') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <label class="control-labell" for="fileList">Danh sách rút thăm:</label>
                    <div class="form-group">
                        <input type="file" class="form-control" name="fileList">
                    </div>
                    <button type="submit" class="btn btn-danger">Nhập dữ liệu</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('footer')
@endpush