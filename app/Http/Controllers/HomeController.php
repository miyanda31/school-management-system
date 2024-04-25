<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        if (Auth::guest()) return view('auth.login');

        $icons = [
            'Admin'=>'dw-user-13',
            'Student'=>'dw-add-user',
            'Parent'=>'dw-user-11',
            'Teacher'=>'dw-user-1',
            ];

        $colors = [
            'Admin'=>'#00eccf',
            'Student'=>'#ff5b5b',
            'Parent'=>'',
            'Teacher'=>'#09cc06',
            ];

        $users = User::groupBy('type')
            ->select(DB::raw('count(*) as total'),'type')
            ->where('type','<>','Librarian')
            ->orderBy('type')
            ->get()
            ->map(function ($data) use ($colors, $icons) {
            return [
                'type'=>$data->type,
                'total'=>$data->total,
                'icon'=>$icons[$data->type],
                'color'=>$colors[$data->type],
            ];
        });

        $user = User::fullname()->find(Auth::id());

       $terms = Term::where('status','<>',0)->select('id','number','calendar_id')->withCount(['calendar as year'=>function($s){
            $s->select('academic');
        }])->get();
        $packages = Fee::get();



        return inertia('Dashboard',compact('users','user','terms','packages'));
    }

    public function logout()
    {
        Auth::logout();
        redirect()->to('home');
    }



}
