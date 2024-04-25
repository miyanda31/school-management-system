<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Register;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $term = Term::status()->first();

        $statuses = ['Assessment','Notes','Not Activity','Lesson','Group Work'];

        $form = Form::class()->with('users:fname,lname,id','monitor:fname,lname,avatar,id','monitress:fname,lname,avatar,id');

       $form = \request()->f ? $form->find(\request()->f) : $form->orderBy('number')->first();

        $classes = Form::with('users:fname,lname,id','monitor:fname,lname,avatar,id','monitress:fname,lname,avatar,id')->get();

        $date = now();

        $all = array();
        foreach ($statuses  as $status ) {
            $results =  Register::whereHas('timetable',function ( $q ) use ($form, $term ) {
                $q->whereHas('allocation',function ($w)use ($form, $term) {
                    $w->whereFormId($form->id)->whereTermId($term->id);
                });
            })->whereDate('created_at',$date)->whereActivity($status)->count();
            $all[$status] = $results;
        }



        $register =  Register::whereHas('timetable',function ( $q ) use ($form, $term ) {
            $q->whereHas('allocation',function ($w)use ($form, $term) {
                $w->whereFormId($form->id)->whereTermId($term->id);
            });
        })->with(['timetable'=> function($q) {
            $q->with('allocation.user:id,lname,fname')->with('allocation.subject:id,name')->with('time:id,start,end,altend,altstart');
        }]);

        $register = \request()->s ? $register->whereDate('created_at', '>=', \request()->s)->whereDate('created_at', '<=', \request()->e) : $register->whereDate('created_at', '>=', $date);

        if (\request()->u) {
            $register = $register-> whereHas('timetable',function ( $q ) use ($form, $term ) {
                $q->whereHas('allocation',function ($w)use ($form, $term) {
                    $w->whereUserId(\request()->u);
                });
            });
        }

        $register = $register->get();

        $users = User::adminOrTeacher()->fullname()->get();

        return inertia('Attendance/PeriodRegister',compact('register','all','classes','form','statuses','users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Http\Response
     */
    public function show(Register $register)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Http\Response
     */
    public function edit(Register $register)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Register $register)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Http\Response
     */
    public function destroy(Register $register)
    {
        //
    }
}
