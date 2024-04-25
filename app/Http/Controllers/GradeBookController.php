<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Form;
use App\Models\Grading;
use App\Models\Paper;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;

class GradeBookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $term = Term::status()->first();

        $classes = Form::whereHas('grade',function ($s) use ($term){
            $s->whereTermId($term->id);
        })->get();

        return inertia('Examinations/GradeBook',compact('term','classes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = Form::find($request->f);

        $systems = Grading::whereType($form->number>2?'MSCE':'JCE')->get();

        $grades = Grade::whereSubjectId($request->s)
            ->whereFormId($request->f)
            ->whereTermId($request->t)->get();

        foreach ($grades as $grade) {

            $score = array_sum($grade->scores);

            foreach ($systems as $system) {

                if ($score >= $system->min && $score <= $system->max) {

                    $grade->update([
                        'score'=>$score,
                        'status'=>1,
                        'grading_id'=>$system->id
                    ]);
                }

            }

        }

        $grades = Grade::whereSubjectId($request->s)
            ->whereFormId($request->f)
            ->whereTermId($request->t)
            ->orderByDesc('score')->get();

        $rank = 1;
        $previous = null;
        foreach ($grades as $score) {
            if ($previous != null && $previous->score != $score->score) $rank++;

            if ($score->score != null) {
                $score->position = $rank;
                $previous = $score;
                $score->save();
            }

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show($form)
    {
        $subjects = Subject::has('schools')->select('id','name')->get();

        $form = Form::with(['user'=>function($s){
            $s->selectable();
        }])->find($form);
        $term = Term::status()->first();

        $papers = [];
        $status = 0;
        $users = [];
        $allocation = [];
        if (\request()->s) {
            $papers = Paper::whereSubjectId(\request()->s)->get();
            $status = Grade::whereTermId(request()->t)->whereSubjectId(\request()->s)->whereFormId($form->id)->first()->status??0;
            $users = User::simple()->with(['grade'=>function ( $q ) use ( $form ) {
                $q->select('score','user_id','grading_id','subject_id','scores')
                    ->whereTermId(request()->t)
                    ->whereSubjectId(\request()->s)
                    ->whereFormId($form->id)
                    ->with(['grading'=>function ( $q ){
                    $q->select('grade','remark','id');
                }]);
            }])->whereHas('form',function ( $q ) use ( $form ) {
                $q ->whereId($form->id);
            })->student()->active()->orderBy('gender')->orderBy('lname')->paginate(30);
            $users->setPath(\request()->fullUrl());
            $allocation = Allocation::with(['user'=>function($s){
                $s->selectable();
            }])->whereTermId(request()->t)->whereSubjectId(\request()->s)->whereFormId($form->id)->first();

        }




        return inertia('Examinations/Grades',compact('subjects','users','form','status','papers','term','allocation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $grade)
    {
         Grade::whereTermId(request()->t)->whereSubjectId(\request()->s)->whereFormId($grade)

            ->update([
            'score'=>null,
            'grading_id'=>null,
            'position'=>null,
            'status'=>0,
        ]);
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
