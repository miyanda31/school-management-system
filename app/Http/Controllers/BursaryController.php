<?php

namespace App\Http\Controllers;

use App\Models\Bursary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BursaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
            $students = User::whereHas('form',function($q){
                $q->whereId(\request()->c);
                 })
                ->fullname()
                ->student()->active()
                ->doesntHave('bursary')
                ->get();

            return response()->json($students);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $bursary = Bursary::find($request->id);

            $bursary->user()->syncWithoutDetaching($request->selected);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bursary  $bursary
     * @return \Illuminate\Http\Response
     */
    public function show(Bursary $bursary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bursary  $bursary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bursary $bursary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bursary  $bursary
     * @return \Illuminate\Http\Response
     */
    public function destroy($bursary)
    {

        $user = User::find($bursary);
        $user->bursary()->detach();
    }
}
