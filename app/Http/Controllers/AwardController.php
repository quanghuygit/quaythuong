<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Contract;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['awards'] = Award::all();
        return view('admin.award.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.award.create_edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            Award::query()->create($request->all());

            flash()->success('Tạo giải thưởng thành công');
            DB::commit();
            return redirect()->route('award.index');
        } catch (\Exception $e) {
            flash()->error('Không thể tạo giải thưởng.');
            DB::rollBack();
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $award = Award::query()->find($id);
            if (!$award) {
                throw new \Exception('Không tìm thấy giải thưởng này.');
            }

            $data['award'] = $award;

            return view('admin.award.create_edit', $data);
        } catch (\Exception $exception) {

            flash()->error('Không thể chỉnh sửa giải thưởng này');
            return redirect()->route('award.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $award = Award::query()->find($id);
            if (!$award) {
                throw new \Exception('Không tìm thấy giải thưởng này.');
            }

            if (!$award->update($request->all())) {
                throw new \Exception('Không thể cập nhật giải thưởng này.');
            }

            flash()->success('Cập nhật giải thưởng thành công.');
            DB::commit();
            return redirect()->route('award.index');
        } catch (\Exception $e) {
            flash()->error('Không thể tạo giải thưởng.');
            DB::rollBack();
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $award = Award::query()->find($id);
            if (!$award) {
                throw new \Exception('Không tìm thấy giải thưởng này.');
            }

            if (!$award->delete()) {
                throw new \Exception('Không thể cập nhật giải thưởng này.');
            }

            flash()->success('Xóa giải thưởng thành công.');
            DB::commit();
            return redirect()->route('award.index');
        } catch (\Exception $e) {

            flash()->error('Không thể xóa giải thưởng.');
            DB::rollBack();
            return redirect()->route('award.index');
        }
    }

    public function winners(Request $request)
    {
        $data['winners'] = Award::with('contract')->get();
        return view('admin.award.winner', $data);
    }

    public function createWinner(Request $request, $awardId)
    {
        try {
            $award = Award::query()->find($awardId);
            if (!$award) {
                throw new \Exception('Không tìm thấy giải thưởng này.');
            }

            $data['award'] = $award;
            $data['awards'] = [$award->id => $award->name];
            $data['contracts'] = Contract::notWin()->get();
            $data['tvkt'] = Contract::query()->pluck('tvkt', 'id')
                ->prepend('Chọn tư vấn khai thác', '')
                ->map(function ($item) {
                    return ucwords(strtolower($item));
                })
                ->unique();

            $data['winners'] = view('admin.common.contract.list_tvkt', ['winners' => $award->contract]);

            return view('admin.award.create_edit_winner', $data);
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->route('award.index');
        }

    }

    public function storeWinner(Request $request)
    {
        try {
            DB::beginTransaction();
            $awardId = $request->get('award_id', null);
            $award = Award::findOrFail($awardId);

            $contractWinners = $request->get('contract_winners');

            if ($award->contract()->count() > 0 && empty($contractWinners)) {
                $contractWinners = [];
            }

            $award->contract()->sync($contractWinners);

            flash()->success('Thêm người trúng thưởng vào giải thành công.');
            DB::commit();
            return redirect()->route('award.winners');
        } catch (\Exception $e) {
            flash()->error('Không thể thêm người trúng giải.');
            DB::rollBack();
            redirect()->back();
        }
    }

    public function ajaxtvkt(Request $request)
    {

        try {
            $idContract = $request->get('idContract', null);
            $contract = Contract::query()->findOrFail($idContract);

//            throw new \Exception('abc');
            $data['contract'] = $contract;
            return view('admin.common.contract.tvkt', $data)->render();
        } catch (\Exception $e) {
            return json(['message' => 'không tìm thấy tư vấn khai thác này.'], 400);
        }
    }
}
