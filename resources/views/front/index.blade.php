@extends('layout.front.index')

@section('main-content')
    <div class="top-option">
        <div class="panel panel-white">
            <div class="panel-heading clearfix">
                <h4 class="panel-title"><strong>Cài đặt quay thưởng</strong></h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Chế độ quay:</label>
                    </div>
                    <div class="col-md-4">
                        <label>
                            <input type="radio" name="optionsRadios" class="wheel_type" value="1" checked>
                            Quay thử
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label>
                            <input type="radio" name="optionsRadios" class="wheel_type" value="2">
                            Quay thật
                        </label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Giải thưởng:</label>
                    </div>
                    @foreach($winners as $award)
                    <div class="col-md-3">
                        <label>
                            <input type="radio" name="award_type" class="award no-uniform" value="{{ $award->id }}">
                            {{ $award->name }} (<span class="countLeft countLeft-{{ $award->id }}">{{ $award->number }}</span>)
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="areaResult">
        <div class="panel panel-white">
            <div class="panel-heading clearfix">
                <h4 class="panel-title"><strong>Kết quả</strong></h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="tbResult" class="display table" style="width: 100%; cellspacing: 0;">
                        <thead>
                        <tr>
                            <th class="f-bold">STT</th>
                            <th class="f-bold">Số HĐ</th>
                            <th class="f-bold">Chủ HĐ</th>
                            <th class="f-bold">TVGT</th>
                            <th class="f-bold">TVKT</th>
                            <th class="f-bold">Mã Code</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="areaWheel">
        <div class="row">
            <div class="col-md-10">
                <div class="panel panel-white">
                    <div class="panel-heading clearfix">
                        <h4 class="panel-title"><strong>Danh sách quay thưởng</strong></h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="tbShuffer" class="display table" style="width: 100%; cellspacing: 0; position: relative">
                                <thead>
                                <tr>
                                    <th class="f-bold">STT</th>
                                    <th class="f-bold">Số HĐ</th>
                                    <th class="f-bold">Chủ HĐ</th>
                                    <th class="f-bold">TVGT</th>
                                    <th class="f-bold">TVKT</th>
                                    <th class="f-bold">Mã Code</th>
                                </tr>
                                </thead>
                                <tbody >
                                @if (!empty($contracts))
                                    @foreach($contracts as $contract)
                                        <tr>
                                            <td>{{ $contract->id }}</td>
                                            <td>{{ $contract->contract_id }}</td>
                                            <td>{{ $contract->contract_user_name }}</td>
                                            <td>{{ $contract->tvgt }}</td>
                                            <td>{{ $contract->tvkt }}</td>
                                            <td>{{ $contract->code }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-center">
                    <button class="btn btn-success btn-block btnStart">Quay thưởng</button>
                    <div class="clearfix"></div>
                    <button class="btn btn-warning btn-block btnStop" style="margin-top: 10px">Dừng quay</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <link href="{{ asset('admin/plugins/datatables/css/jquery.datatables.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/plugins/datatables/css/jquery.datatables_themeroller.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js') }}"></script>
{{--    <script src="{{ asset('front/plugins/datatables/dataTables.scroller.min.js') }}"></script>--}}
{{--    <script src="{{ asset('https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js') }}"></script>--}}
    <style>
        .panel-title {
            padding: 0 0 5px;
        }

        table.dataTable thead th, table.dataTable thead td,
        .dataTables_wrapper.no-footer .dataTables_scrollBody {
            border: none;
        }
        .panel {
            padding: 15px
        }
    </style>
    <script type="text/javascript">
        var counterWinner = {!! $winners->pluck('number','id') !!};

        var tblShuffer;
        $(document).ready(function() {
            configs = {
                scrollY:        200,
                scrollCollapse: true,
                scroller:       true,
                "paging": false,
                bSort : false,
                searching: false,
                language: {
                    "decimal": "",
                    "emptyTable": "",
                    "info": "",
                    "infoEmpty": "",
                    "infoFiltered": "",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "",
                    "loadingRecords": "",
                    "processing": "",
                    "search": "",
                    "zeroRecords": "",
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
                },
            };

            tblShuffer = $('#tbShuffer').DataTable(configs);

            tblHeight = $('#tbShuffer').height() - 200;
            startP = 0;

            var total = $('#tbShuffer tbody tr').length;
            var i = 0;
            var isStop = triggerDone = false;
            var timeout1 = timeout2 = null;

            function myLoop () {
                console.log('curent i: '+ i);
                if (isStop == true) {
                    window.clearTimeout(timeout1);
                    window.clearTimeout(timeout2);
                    console.log($('#tbShuffer tbody tr.selected'));

                    if (triggerDone) {
                        return false;
                    }
                    triggerDone = true;
                    $('body').trigger('finishShuffler');
                    return;
                }

                timeout1 = setTimeout(function () {
                    if (isStop == true) {
                        if (triggerDone) {
                            return false;
                        }
                        triggerDone = true;
                        $('body').trigger('finishShuffler');
                        return;
                    }

                    if (i < total) {            //  if the counter < 10, call the loop function
                        myLoop();             //  ..  again which will trigger another
                    } else {
                        i = 0;
                        myLoop();
                    }
                    $('#tbShuffer tbody tr').removeClass('selected');
                    $('#tbShuffer tbody tr').eq(i).addClass('selected');

                    i++;
                }, 5);

                timeout2 = setTimeout(function () {    //  call a 3s setTimeout when the loop is called
                    console.log('scroll: ' + startP + ' height: '+(tblHeight+20));
                    console.log('has selected: '+ $('#tbShuffer tbody tr').last().hasClass('selected'));

                    if (isStop == true) {
                        if (triggerDone) {
                            return false;
                        }
                        triggerDone = true;
                        $('body').trigger('finishShuffler');
                        return;
                    }

                    if (Math.abs(startP) >= tblHeight+20 && $('#tbShuffer tbody tr').last().hasClass('selected')) {
                        $('#tbShuffer tbody tr').removeClass('selected');
                        startP = 0;
                        $('#tbShuffer').css({
                            top: startP
                        });
                    } else {
                        $('#tbShuffer').css({
                            top: startP
                        });
                        startP -= 37;
                    }
                }, 10);
            }

            function resetScroll() {
                $('#tbShuffer tbody tr').removeClass('selected');
                startP = 0;
                i = 0;
                isStop = false;
                triggerDone = false;
                $('#tbShuffer').css({
                    top: startP
                });
            }

            function setWheelAward() {
                avaiableAwards = $('.award:enabled');
                console.log(avaiableAwards);
                avaiableAwards.first().prop('checked', true);
            }

            function checkAward() {
                currentAward = $('.award:checked');
                counter = $('.countLeft-' + currentAward.val());
                numLeft = parseInt(counter.text()) -1;
                console.log(currentAward, counter, numLeft);
                counter.text(numLeft);
                if (numLeft <= 0) {
                    currentAward.prop('disabled', true);
                    setWheelAward();
                    return;
                }

            }

            $('.btnStart').on('click', function() {
                resetScroll();
                $('#tbResult tbody').html("");
                myLoop();
            });

            $('.btnStop').on('click', function(e) {
                e.preventDefault();
                isStop = true;
            });
            
            $('body').on('finishShuffler', function(e) {
                checkAward();
                console.log('shuffle');
                console.log($('#tbShuffer tbody tr.selected'));
                $('#tbResult tbody').html($('#tbShuffer tbody tr.selected').first());
            });
        } );
    </script>
@endpush