<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Score;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $term1_scores = Grade::whereUserId(\request()->u)->whereTermId(\request()->t)->withCount(['subject as subject'=>function($s){
            $s->select('short_code');
        }])->get();

        $term2_scores = Grade::whereUserId(\request()->u)->whereTermId(\request()->t2)->withCount(['subject as subject'=>function($s){
            $s->select('short_code');
        }])->get(['score','subject_id']);

        $subjects = [];
        $t1Scores = [];
        $t2Scores = [];
        foreach ($term1_scores as $term1_score) {
            foreach ($term2_scores as $term2_score) {
                if ($term2_score->subject_id == $term1_score->subject_id) {
                    $t1Scores[] = $term1_score->score;
                    $t2Scores[] = $term2_score->score;
                    $subjects[] = $term1_score->subject;
                }
            }
        }

        $term1_avg = round(array_sum($t1Scores)/count($subjects),0) ;
        $term2_avg = round(array_sum($t2Scores)/count($subjects),0) ;

        return response()->json([
            'subjects'=>$subjects,
            't1'=>$t1Scores,
            't2'=>$t2Scores,
            't1Avg'=>$term1_avg,
            't2Avg'=>$term2_avg,
            'tn1'=>Term::find(\request()->t)->number,
            'tn2'=>Term::find(\request()->t2)->number
        ]);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show($id)
    {
        $user = User::fullname()->find($id);
        $terms = Term::select('id','number','calendar_id')->withCount(['calendar as year'=>function($s){
            $s->select('academic');
        }])->where('status','<>',0)->get();
        return inertia('Students/Performance',compact('user','terms'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
