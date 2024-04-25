<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class ReportAnalysisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $grades = Grade::select('score') ;


        if (\request()->p) {

            $grades = $grades->whereTermId(\request()->s)->withCount(['subject as subject'=>function($s) {
                $s->select('short_code');
            }])->get();

            $grades2 = $grades->whereTermId(\request()->e)->withCount(['subject as subject'=>function($s) {
                $s->select('short_code');
            }])->get();

            response()->json(\request()->e ?[$grades,$grades2]:$grades);
        }

        $classes = Form::class()->get();

        return inertia('Examinations/Analysis',compact('classes'));
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($grade)
    {
        $users = User::search($grade)->fullname()->with(['scores'=>function($s){
            $s->select('term_id','user_id')->with(['term'=>function($s){
               $s->select('id','number','calendar_id')
                   ->withCount(['calendar as year'=>function($s){
                   $s->select('academic');
               }]);
            }]);
        }])->student()->active()->get();
       return response()->json($users);
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
