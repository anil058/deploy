<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use DataTables;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class UsermanagerController extends Controller{

    public function index(Request $request)
    {
        // if ($request->ajax()) {
        //     $data = User::latest()->get();
        //     return Datatables::of($data)
        //             ->addIndexColumn()
        //             ->addColumn('action', function($row){

        //                    $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';

        //                     return $btn;
        //             })
        //             ->rawColumns(['action'])
        //             ->make(true);
        // }

        return view('users');
    }

}
