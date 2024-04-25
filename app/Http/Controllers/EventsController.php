<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{
    public function validation($request)
    {
        $rules =[
            'type'=>'sometimes|required',
            'title'=>'sometimes|required',
            'start'=>'sometimes|required',
            'end'=>'sometimes|required',
            'description'=>'sometimes|required',
        ];
        $messages =[
            'required'=>'This is a required field'
        ];
        $this->validate($request,$rules,$messages);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        if (\request()->s) {
            $calendars = Event::select('title','description','id','start','end','type','color')
                ->whereBetween('start',[\request()->s,\request()->e]);

            if (\request()->t) {
                $calendars =$calendars->whereType(\request()->t);
            }

            $calendars =$calendars->get();

            return response()->json($calendars);
        }

        $term = Term::status()->first();
        $status = Event::whereStatus(0)->count();

        return inertia('Events',compact('term','status'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $colors = [
            'School'=>'#28a745',
            'Public'=>'#f56767',
            'Department'=>'#00e091',
            'Administrative'=>'#1b00ff',
            'Club'=>'#17a2b8',
        ];
         Event::updateOrCreate([
             'start'=>$request->day.' '.$request->start,
            'end'=>$request->end,
            'title'=>$request->title,
            'type'=>$request->type,
            'description'=>$request->description,
            'color'=>$colors[$request->type],
            'user_id'=>Auth::id(),
            'status'=>0,
            'term_id'=>Term::status()->first()->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        if ($request->u) {

            $event->update([
                'start'=>$request->s,
                'end'=>$request->e,
            ]);
            return;
        }
        $colors = [
            'School'=>'#28a745',
            'Public'=>'#f56767',
            'Department'=>'#00e091',
            'Administrative'=>'#1b00ff',
            'Club'=>'#17a2b8',
        ];

        $event->update([
            'title'=>$request->title,
            'description'=>$request->description,
            'start'=>$request->day.' '.$request->start,
            'end'=>$request->end,
            'type'=>$request->type,
            'color'=>$colors[$request->type],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
