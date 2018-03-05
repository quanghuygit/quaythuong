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
                            <input type="radio" data-id="{{ $award->id }}" name="award_type" class="award award-{{ $award->id }} no-uniform" data-real="{{ $award->left }}" data-temp="{{ $award->number }}" data-limit="{{ $award->number }}" value="{{ $award->id }}" @if($loop->first) checked @endif>
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
    <script src="{{ asset('front/plugins/visible/visible.js') }}" type="text/javascript"></script>
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
        var views = {!! $views !!};
        var counterWinner = {!! $winners->pluck('left','id') !!};
        var tempCounterWinner = {!! $winners->pluck('number','id') !!};

        var realCount = null,
            tempCount = null;
        function checkCount() {
            if (localStorage.hasOwnProperty('tempCount')) {
                tempCount = localStorage.getItem('tempCount');
            } else {
                tempCount = tempCounterWinner;
            }

            localStorage.setItem('tempCount', tempCount);
        }

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
                }, 100);

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

                    if (
                        (Math.abs(startP) >= tblHeight) && $('#tbShuffer tbody tr').last().hasClass('selected') === false
                    ) {
                        return;
                    }

                    startP = -(i*37);
                    if (
                        $('#tbShuffer tbody tr').last().hasClass('selected')
                    ) {
                        $('#tbShuffer tbody tr').removeClass('selected');
                        startP = 0;
                    }
                    $('#tbShuffer').css({
                        top: startP
                    });
                }, 200);
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
                avaiableAwards.first().prop('checked', true);
                if (avaiableAwards.length <= 0) {
                    $('.btnStop').prop('disabled', true);
                }
            }

            function checkAward() {
                currentAward = $('.award:checked');
                counter = $('.countLeft-' + currentAward.val());
                numLeft = parseInt(counter.text()) -1;

                counter.text(numLeft);

                if (parseInt($('.wheel_type:checked').val()) == 2) {
                    realLeft = parseInt(currentAward.attr('data-real'));
                    currentAward.attr('data-real', realLeft - 1);
                    counter.text(realLeft - 1);
                } else {
                    currentAward.attr('data-temp', numLeft);
                    counter.text(numLeft);
                }

                if (numLeft <= 0) {
                    currentAward.prop('disabled', true);
                    setWheelAward();
                    return;
                }
            }

            function checkRealCount() {
                currentAward = $('.award:checked');


                $.each($('.award'), function(key, item) {
                    adward = $(item);
                    numLeft = 0;
                    id = adward.attr('data-id');
                    adward = $('.award-'+id);
                    counter = $('.countLeft-' + id);
                    if (parseInt($('.wheel_type:checked').val()) == 2) {
                        numLeft = parseInt(adward.attr('data-real'));
                        adward.attr('data-real', numLeft);
                        counter.text(numLeft);
                    } else {
                        numLeft = parseInt(adward.attr('data-temp'));
                        adward.attr('data-temp', numLeft);
                        counter.text(numLeft);
                    }

                    console.log(numLeft);
                    if (numLeft <= 0) {
                        adward.prop('disabled', true);
                        setWheelAward();
                        return;
                    }
                });

            }

            function setWinner() {
                currentAward = $('.award:checked');
                awardType = currentAward.val();

                contracts = views['award'+awardType];
                winned = false;
                selectContract = null;
                for (var contract in contracts) {
                    if (contracts[contract].wined === false) {
                        winned = true;
                        contracts[contract].wined = true;
                        selectContract = contracts[contract];
                        break;
                    }
                }

                selected = $('#tbShuffer tbody tr.selected').first();
                if ($('.wheel_type:checked').val() == 1) {
                    $('#tbResult tbody').html(selected.html());
                    return;
                }

                $.ajax({
                   url: '{{ route('front.ajaxUpdateLeft') }}',
                   method: 'POST',
                   data: {'_token': '{{ csrf_token() }}', awardId: awardType},
                   async: false
                });

                    console.log(contracts[contract].view);
                v1 = $(contracts[contract].view).insertBefore(selected);
                $('#tbShuffer tbody tr').removeClass('selected');
                $(v1).removeClass('selected').addClass('selected');
                $('#tbResult tbody').html(contracts[contract].view);
            }

            checkRealCount();

            $('.btnStart').on('click', function() {
                if ($('.award:enabled').length <= 0) {
                    alert('Đã hết giải, không thể tiếp tục quay thưởng.');
                    return;
                }

                resetScroll();
                $('#tbResult tbody').html("");
                $(this).prop('disabled', true);
                $('.btnStop').prop('disabled', false);
                myLoop();
            });

            $('.btnStop').on('click', function(e) {
                e.preventDefault();
                isStop = true;
                $(this).prop('disabled', true);
                $('.btnStart').prop('disabled', false);
            });

            $(document).on('keypress', function(e) {
                if(e.which == 13) {
                    isStop = true;
                    $(this).prop('disabled', true);
                    $('.btnStart').prop('disabled', false);
                }
            });
            
            $('body').on('finishShuffler', function(e) {
                setWinner();
                checkAward();
            });

            $('.wheel_type').on('change', function(e) {
                oldVal = $('.wheel_type:checked').val();
                newVal = null;

                checkRealCount();
                if (confirm('Bạn có chắc chắn chuyển qua chế độ quay thưởng này?')) {

                } else {
                    newVal = $('.wheel_type:checked').val();
                    switchs = newVal > 1 ? 1 : 2;
                    $('.wheel_type[value='+switchs+']').prop('checked', true);
                }
            });
        } );
    </script>
@endpush