<tr data-id="{{ $contract->id }}" class="row-winner-{{ $contract->id }}">
    <td>{{ $contract->contract_id }}</td>
    <td>{{ $contract->contract_name }}</td>
    <td>{{ $contract->contract_user_name }}</td>
    <td>{{ $contract->tvgt }}</td>
    <td class="f-bold bg-n text-white">{{ $contract->tvkt }}</td>
    <td>{{ $contract->code }}</td>
    <td>
        {!! Form::hidden('contract_winners[]', $contract->id) !!}
        <button type="button" class="btn btn-danger btn-remove-tvkt" data-id="{{ $contract->id }}" data-contract-id="{{ $contract->contract_id }}">Xóa</button>
    </td>

</tr>