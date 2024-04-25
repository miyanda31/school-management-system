<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Calendar;
use App\Models\Term;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
       $calendar = Calendar::whereHas('term',function ($q){
            $q->status();
        })->first();

       if (\request()->t) return view('academic',compact('calendar'));

       $events = Event::whereStatus(1)->whereType('public')->whereNotNull('description->main')->whereTermId(Term::status()->first()->id)->get();
        return view('calendar',compact('calendar','events'));
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
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function show($cal )
    {
        $calendar = Calendar::whereHas('term',function ( $q ){
            $q->status();
        })->first();

        if ($cal  == 'activities'){

            $events = Event::whereType('public')->whereNotNull('description->main')->get();

            return view('about.events',compact('events','calendar'));
        }

        if ($cal  == 'calendar'){
            return view('about.calendar',compact( 'calendar'));
        }

        $events = Event::whereDate('start','>=', request()->start)->whereDate('end','<=',request()->end)->whereStatus(1)->whereType('public')->whereTermId(Term::status()->first()->id)->get();

        return response()->json($events,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Calendar $calendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calendar $calendar)
    {
        //
    }
}
