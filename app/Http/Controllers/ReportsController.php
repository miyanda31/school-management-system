<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Grade;
use App\Models\School;
use App\Models\Score;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        if (\request()->r) {

            $users = User::simple()->with(['score'=>function ($q)  {
                $q->whereTermId(request()->t);
            }])->withCount(['score as position'=>function ($q)  {
                $q->select('class_position');
            }])->with(['grades'=>function ($q)  {
                $q->whereTermId(request()->t);
            }])->whereHas('form',function ( $q ){
                $q->whereId(\request()->f);
            })->active()->student()->orderBy('position')->get();

            $form = Form::withCount(['user as enrollment'=>function( $q ){
                $q->active()->student();
            }])->find(\request()->f);

            $term = Term::find(request()->t);
            $type = Grade::whereTermId(request()->t)->whereNotNull('scores')->whereFormId($form->id)->first(['type']);

            $name = PDF::loadView('assessments.reports',compact('users','term','type','form'),[]);

            return $name->stream('Form '.$form->number.$form->name.'School  Reports.pdf');
        }

        if (\request()->m) {
            $users = User::simple()->with(['grades'=>function ( $q )  {
                $q->select('score','user_id','grading_id','subject_id')->whereTermId(request()->t)->whereFormId(\request()->f)->withCount(['grading as grade'=>function ( $q ){
                    $q->select('grade');
                }]);
            }])->whereHas('form',function ( $q ) {
                $q ->whereId(\request()->f);
            })->student()->active()->get();

            $form = Form::find(request()->f);
            $subjects = Subject::has('schools')->get(['id','short_code']);
            $name = PDF::loadView('assessments.grades',compact('users','form','subjects'),[],['format'=>'A4-L']);
            return $name->stream('Form'.$form->number.$form->name.'Marksheet.pdf');
        }

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
     * @param  int  $grade
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(int $grade)
    {
        $term = Term::select('number','id','calendar_id');

        $term = \request()->t ? $term->find(\request()->t) : $term->first();

        $form = Form::whereHas('user',function ( $q ) use ($grade) {
            $q->whereId($grade);
        })->first();

            $user =  User::select('id','student_id','avatar',DB::raw('concat(fname," ",lname) as name'),'gender')
                ->withCount(['grades as written'=>function($q) use ($term) {
                    $q->whereTermId($term->id);
                }])
                ->with(['grades'=>function($q) use ($term) {
                    $q->with('grading')->with('subject:id,name')->whereTermId($term->id);
                }])
                ->find($grade) ;

            $scores = Score::whereUserId($grade)->whereTermId($term->id)->first();

            $total = $form->user()->student()->count();
            $school = School::first();

        return inertia('Examinations/Report',compact('user','term','form','scores','total','school'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Grade $grade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function destroy(Grade $grade)
    {
        //
    }
}
