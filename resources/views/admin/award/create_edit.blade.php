@extends('layout.admin.index')

@section('main-content')
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading clearfix">
                <h4 class="panel-title">
                    @if (!empty($award))
                        Cập nhật giải thưởng.
                    @else
                        Thêm giải thưởng
                    @endif
                </h4>
            </div>
            <div class="panel-body">
                @if (!empty($award))
                    {!! Form::model($award, ['route' => ['award.update', $award->id], 'method' => 'PUT', 'class' => 'form-horizontal']) !!}
                @else
                    {!! Form::open(['route' => 'award.store', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
                @endif
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Tên giải thưởng</label>
                        <div class="col-sm-10">
                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Tên giải thưởng']) !!}
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Số người trúng</label>
                        <div class="col-sm-10">
                            {!! Form::text('number', null, ['class' => 'form-control', 'placeholder' => 'Số người trúng']) !!}
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success">@if (!empty($award)) Cập nhật @else Thêm giải @endif</button>
                        </div>
                    </div>
                </form>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@push('footer')
@endpush