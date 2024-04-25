<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Form;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $today = Carbon::now();

        $classes = Form::class()->whereShift(\request()->s??'A')->with('users:fname,lname,id')->get();

        $form = Form::orderBy('number')->first();
        $term = Term::status()->first();



        if (Carbon::parse($term->closing)->diffInWeeks(Carbon::today()) >= 0) {
            $weeks = Carbon::parse($term->opening)->diffInWeeks($term->closing);
        }
        else {
            $weeks = Carbon::parse($term->opening)->diffInWeeks($today);
        }

        $week = request()->w ? Carbon::parse( $term->opening )->addWeeks( request()->w ) : $today;

        $users= User::student()->status(1);

        if (\request()->g) {
            $users = $users->whereGender(\request()->g);
        }

        $users = $users->orderBy('gender')->orderBy('lname')
            ->with(['attendance'=>function($q) use ( $week, $term) {
                $q->select(DB::raw('date_format(created_at,"%a") as days'),'user_id')
                    ->whereDate('created_at','>=',$week->startOfWeek()->toDateString())->whereDate('created_at','<=',$week->endOfWeek()->toDateString())
                    ->whereTermId($term->id);
            }])->whereHas('form',function ($s) use ($form) {
                $s->whereFormId(\request()->f ?\request()->f :$form->id);
            })->paginate(\request()->n??10);;

        $users->setPath(\request()->fullUrl());

        $counts = Attendance::whereFormId(\request()->f ?\request()->f :$form->id)
            ->whereDate('created_at','>=',$week->startOfWeek()->toDateString())->whereDate('created_at','<=',$week->endOfWeek()->toDateString())
            ->whereTermId($term->id);

        $males = $counts->whereHas('user',function ($s) {
            $s->whereGender('Male');
        })->count();

        $females = $counts->whereHas('user',function ($s) {
            $s->whereGender('Female');
        })->count();

        $gender = ['Male','Female'];

        $maleAttendance = array();
        $femaleAttendance = array();
        foreach ( $gender as $item ) {

                $att= Attendance::whereFormId(\request()->f ?\request()->f :$form->id)
                    ->whereTermId($term->id)
                    ->select(DB::raw('date_format(created_at,"%a") as day, count(*) as total'))
                    ->groupBy(DB::raw('date_format(created_at,"%a")'))
                    ->whereHas('user',function ($s) use ($item) {
                        $s->whereGender($item);
                    })
                    ->whereDate('created_at','>=',$week->startOfWeek()->toDateString())->whereDate('created_at','<=',$week->endOfWeek()->toDateString())
                    ->get();

                $item == 'Male'? $maleAttendance = $att : $femaleAttendance = $att;

        }

        return inertia('Attendance/DailyAttendance',compact('users','term','weeks','week','maleAttendance','femaleAttendance','males','females','classes','form'));
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
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show($attendance)
    {
        $term = Term::status()->first();

        $attendances= Attendance::whereUserId($attendance)
            ->whereDate('created_at','>=',$term->opening)
            ->whereDate('created_at','<=',$term->closing)->select(DB::raw('reason, count(*) as total'))->groupBy('reason')
            ->whereTermId($term->id)->get();

        $total = Attendance::whereUserId($attendance)
            ->whereDate('created_at','>=',$term->opening)
            ->whereDate('created_at','<=',$term->closing)
            ->whereTermId($term->id)->count();

        $days = Carbon::parse($term->opening)->diffInWeekdays(Carbon::today());
        $term = Carbon::parse($term->opening)->diffInWeekdays($term->closing);

        if ($days>$term) {
            $days = $term;
        }

        $user =User::simple()->with('form:id,name,number')->find($attendance);



        return inertia('Attendance/StudentAttendance',compact('attendances','user','days','total'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
