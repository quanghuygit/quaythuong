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
        $data['winners'] =  Award::with('contract')->get();

        return view('front.index', $data);
    }
}
