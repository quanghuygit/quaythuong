@foreach($winners as $winner)
    @include('admin.common.contract.tvkt', ['contract' => $winner])
@endforeach