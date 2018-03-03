@extends('layout.admin.index')

@section('main-content')
    <div class="col-md-12">
        {!! Form::open(['route' => 'award.storeWinner', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
        <div class="panel panel-white">
            <div class="panel-heading clearfix">
                <h4 class="panel-title">
                    Chọn giải thưởng
                </h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tên giải thưởng</label>
                    <div class="col-sm-10">
                        {!! Form::select('award_id', $awards, null, ['class' => 'form-control award_id', 'data-limit' => $award->number]) !!}
                        <p class="help-block"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-white">
            <div class="panel-heading clearfix">
                <h4 class="panel-title">Chọn người trúng giải</h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tư vấn khai thác</label>
                    <div class="col-sm-10">
                        {!! Form::select('tvkt', $tvkt, null, ['class' => 'form-control tvkt']) !!}
                        <p class="help-block"></p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="f-bold">Số hợp đồng</th>
                            <th class="f-bold">Chủ hợp đồng</th>
                            <th class="f-bold">NĐBH</th>
                            <th class="f-bold">TVGT</th>
                            <td class="f-bold bg-danger text-white">TVKT</td>
                            <th class="f-bold">CODE</th>
                            <th class="f-bold">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody class="list-winners">
                        @if (!empty($winners))
                            {!! $winners !!}
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel-footer" style="background: none">
                <div class="form-group text-right">
                    <button class="btn btn-success">Lưu lại</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        {!! Form::hidden('number_count', 0, ['id' => 'number_count']) !!}
    </div>
@endsection

@push('footer')
    <script type="text/javascript">
        $(document).ready(function() {
            function checkWinner() {
                $.each( $('.list-winners tr'), function( key, row ) {
                    tvkt = $(row).attr('data-id');

                    $('.tvkt option[value="'+tvkt+'"]').prop('disabled', true);
                    $('.tvkt option[value="'+tvkt+'"]').removeClass('hide').addClass('hide');
                });
            }

            checkWinner();
            $('.tvkt').on('change', function(e) {
                numberCount = $('.list-winners tr').length;
                limit = parseInt($('.award_id').attr('data-limit'));

                if (numberCount >= limit) {
                    alert('Số người trúng giải không được quá '+ limit);
                    return;
                }

                tvkt = $(this).val();
                if (tvkt.length <= 0) {
                    return;
                }

                $.ajax({
                    url: '{{ route('award.ajaxtvkt') }}',
                    method: 'POST',
                    data: {idContract: tvkt, '_token': '{{ csrf_token() }}'},
                    dataType: 'HTML',
                    success: function(res) {
                        $('.list-winners').append(res);

                        $('.tvkt option[value="'+tvkt+'"]').prop('disabled', true);
                        $('.tvkt option[value="'+tvkt+'"]').removeClass('hide').addClass('hide');

                        $('.tvkt').val('');
                    },
                    error: function() {
                        alert('looix');
                    }
                }).done(function(res) {
                }).fail(function(res) {
                })
            });

            $(document).delegate('.btn-remove-tvkt', 'click', function(e) {
                e.preventDefault();

                tvkt = $(this).attr('data-id');
                $(this).closest('tr').remove();

                $('.tvkt option[value="'+tvkt+'"]').prop('disabled', false);
                $('.tvkt option[value="'+tvkt+'"]').removeClass('hide');
            })
        })
    </script>
@endpush