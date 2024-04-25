<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Grade;
use App\Models\Score;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;

class MarkSheetsController extends Controller
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
            $s->whereTermId($term->id)->whereStatus(1);
        })->get();

        return inertia('Examinations/MarkSheets',compact('term','classes'));
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
     * @param  \App\Models\Grade  $grade
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show($grade)
    {
       $terms = Term::withCount(['calendar as year'=>function($s) {
            $s->select('academic');
        }])->get();

       $term = Term::find(\request()->t);

       $classes = Form::class()->get();
       $subjects = Subject::has('schools')->get(['id','short_code']);

       $form = Form::find($grade);

        $users = User::simple()->with(['grades'=>function ( $q ) use ( $grade ) {
            $q->select('score','user_id','grading_id','subject_id')->whereTermId(request()->t)->whereFormId($grade)->withCount(['grading as grade'=>function ( $q ){
                $q->select('grade');
            }]);
        }])->whereHas('form',function ( $q ) use ( $grade ) {
            $q ->whereId($grade);
        })->student()->active()->orderBy('gender')->orderBy('lname');


        $status = Score::whereTermId(request()->t)->whereHas('grade',function ( $q ) use ( $grade ) {
            $q->whereFormId($grade)->whereTermId(request()->t);
        })->count();


        $users = $users->paginate(30);
        $users->setPath(\request()->fullUrl());

        return inertia('Examinations/MarkSheet',compact('users','status','terms','classes','form','subjects','term'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $grade
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $grade)
    {
        $form = Form::find($grade);
        $term =Term:: find(request()->t);


//         calculate position in class
        $users =  User::select('id')->whereHas('form',function ($query) use ($grade) {
            $query->whereId($grade);
        })->withCount(['grades as total'=>function($q) use ($term, $grade) {
            $q ->whereFormId($grade)
                ->whereTermId($term->id)->select(DB::raw('sum(score)'));
        }])->student()->active()->orderByDesc('total')->get();

        foreach ($users as $user) {

            $english = Grade:: whereSubjectId(10)->whereTermId($term->id)->whereUserId($user->id)->first();

            // check what class
            if ($form->number > 2) {
                // check if wrote eng and has passed
                if ($english && $english->grading_id>1  && $english->score ) {
                    $others = 0;
                    $eng = 0;
                    $eng = $eng +  $english->grading->grade;


                    $written = $user->grades()->orderByDesc('score')->where('subject_id','<>',10)->where('grading_id', '>',1) ->whereTermId($term->id)->take(5)->get();
                    if ($written->count() == 5) {

                        foreach ($written as $marks) {
                            $others += $marks->grading->grade;

                        }

                        Score::updateOrCreate([
                            'aggregate'=>$eng + $others,
                            'user_id'=>$user->id,
                            'term_id'=>$term->id,
                            'form_id'=>$form->id,
                            'score'=>$user->total,
                        ]);
                    }

                }
            }
            else {
                // check if wrote eng and has passed
                if ($english && $english->grading_id>17  && $english->score ) {
                    $others = 0;
                    $eng = 0;
                    $eng = $eng +  $english->score;


                    $written = $user->grades()->orderByDesc('score')->where('subject_id','<>',10)->where('grading_id', '>',17) ->whereTermId($term->id)->take(5)->get();
                    if ($written->count() == 5) {

                        foreach ($written as $marks) {
                            $others += $marks->score;
                        }

                        $avg = ($eng + $others)/6;

                        Score::updateOrCreate([
                            'aggregate'=>round($avg),
                            'user_id'=>$user->id,
                            'term_id'=>$term->id,
                            'form_id'=>$form->id,
                            'score'=>$user->total,
                        ]);
                    }

                }
            }
        }

        //form ranking
        $positions  = Score::select('aggregate','id')->whereHas('form',function ($q) use ( $form ) {
            $q->whereNumber($form->number);
        })->whereTermId($term->id)->orderByDesc('aggregate')->get();

        $rank = 1;
        $previous = null;
        foreach ($positions as $score) {
            if ($previous && $previous->aggregate != $score->aggregate) $rank++;

            if ($score->aggregate != null) {
                $score->class_position = $rank;
                $previous = $score;
                $score->save();
            }

        }

        // class ranking

        $ranks  = Score::select('aggregate','id')->whereTermId($term->id) ->whereFormId($form->id)->orderByDesc('aggregate')->get();


        $rank = 1;
        $previous = null;
        foreach ($ranks as $score) {
            if ($previous && $previous->aggregate != $score->aggregate) $rank++;

            if ($score->aggregate != null) {
                $score->position = $rank;
                $previous = $score;
                $score->status = 1;
                $score->save();
            }

        }




//
//        $users = User::whereHas('timetable',function ($q) use ($grade) {
//		    $q->whereFormId($grade);
//	    })->get();
//
//	    Notification::send($users,new ResultsApproved($form));
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
