<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Event;
use App\Models\Term;
use App\Models\User;
use App\Notifications\TaskCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class DepartmentController extends Controller
{

    public function validation($request)
    {
        $rules =[
            'description'=>'required',
            'title'=>'required',
            'start'=>'required',
            'end'=>'required',
        ];
        $messages =[
            'required'=>'This is a required field'
        ];
        $this->validate($request,$rules,$messages);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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

        $event = Event::updateOrCreate([
            'start'=>$request->start,
            'end'=>$request->end,
            'title'=>$request->title,
            'description'=>$request->description,
            'color'=>'#3085d6',
            'type'=>'Department',
            'user_id'=>Auth::id(),
            'status'=>1,
            'term_id'=>Term::status()->first()->id,
        ]);

        $event->department()->attach(3,['type'=>$request->type]);

//        if (count($request->members)>0) {
//            foreach ( $request->members as $user ) {
//                $user = User::find($user);
//                $event->user()->attach($user->id);
//                Notification::send($user,new TaskCreated($event));
//            }
//        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $department)
    {
        foreach (\request()->members as $member) {
            $event = Event::find($member);

            $event->user()->detach();
            $event->department()->detach();
            $event->delete();
        }
    }
}
