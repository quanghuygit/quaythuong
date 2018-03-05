<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Contract;
use Illuminate\Http\Request;

class WheelController extends Controller
{
    public function index()
    {
        $data['contracts'] = Contract::notWin()->get();
//            ->shuffle();
        $data['winners'] =  Award::with('contract')->get();
        $data['views'] =[];
        foreach ($data['winners'] as $award) {
            $data['views']['award'.$award->id] = [];
            foreach ($award->contract as $contract) {
                $html = view('layout.front.include.contract_winner', ['contract_winner' => $contract])->render();
                $data['views']['award'.$award->id]['contract'.$contract->id] = [
                    'view' => $html,
                    'wined' => false,
                    'id' => $contract->id
                ];
            }
        }
        $data['views'] = json_encode($data['views']);

        return view('front.index', $data);
    }

    public function ajaxUpdateLeft(Request $request)
    {
        try {
            $awardId = $request->get('awardId', null);
            $award = Award::query()->find($awardId);
            if (!$award) {
                throw new \Exception('Không thể cập nhật');
            }

            $award->left = $award->left - 1 <= 0 ? 0 : $award->left-1;
            $award->update();

            return \Response::json('success');
        } catch (\Exception $e) {
            return \Response::json([
                'message' => 'Không thể cập nhật' . $e->getMessage()
            ], 400);
        }
    }
}
