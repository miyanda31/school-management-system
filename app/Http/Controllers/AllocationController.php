<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Form;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $classes = Form::class()->get();
        $subjects = Subject::has('schools')->codes()->get();

        $users = User::adminOrTeacher()->fullname()->get();

       $term = Term::status()->first();

        $allocations =    Allocation::with('form:id,number,name','subject:id,name','user:id,fname,lname');

        if (\request()->n) {
            $allocations  = $allocations->whereHas('form',function ($q){
                $q->whereNumber(request()->n);
            });
        }

        if (\request()->s) {
            $allocations  = $allocations->whereHas('subject',function ($q){
                $q->whereId(request()->s);
            });
        }

        $allocations = $allocations ->whereTermId($term->id)->withCount('timetable as load')->paginate(\request()->p??15);

        return inertia('Timetable/Allocations',compact('subjects','classes','allocations','term','users'));
    }

    public function validation($request)
    {
        $rules =[
            'required'=>'Field is required',
        ];

        $this->validate($request,[
            'id'=>'required',
            'subject'=>'required',
            'class'=>'required',
        ],$rules);
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
        $this->validation($request);

        $term = Term::status()->first();
        $allocation = Allocation::whereUserId($request->user_id)->whereTermId($term->id)->first();

        Allocation::updateOrCreate([
            'subject_id'=>$request->subject,
            'user_id'=>$request->id,
            'form_id'=>$request->class,
            'term_id'=>$term->id,
            'code'=>$allocation->code??random_int(1,99),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Allocation  $allocation
     * @return \Illuminate\Http\Response
     */
    public function show(Allocation $allocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Allocation  $allocation
     * @return \Illuminate\Http\Response
     */
    public function edit(Allocation $allocation)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Allocation  $allocation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Allocation $allocation)
    {
        $this->validation($request);


        $allocation->update([
            'subject_id'=>$request->subject,
            'user_id'=>$request->id,
            'form_id'=>$request->class,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Allocation  $allocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Allocation $allocation)
    {
        $allocation->with('timetable')->delete();
    }
}
