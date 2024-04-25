<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Form;
use App\Models\Payment;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $term = Term::whereNotNull('id');




        $term = \request()->t ? $term->find(\request()->t) : $term->status()->first();

        // total collections per term
        if (\request()->c) {
            $package = Fee::withCount(['package as total'=>function($s){
                $s->select(DB::raw('sum(amount)'));
            }])->withCount(['payment as collections'=>function($s) use ($term) {
                $s->select(DB::raw('sum(amount)'))->whereTermId($term->id);
            }]);
            $students = User::student()->active()->count();

            $package = \request()->p ? $package->find(\request()->p) : $package->first();

            return response()->json([
                'students'=>$students,
                'package'=>$package,
            ]);
        }

        $shifts = Form::groupBy('shift');

        if (\request()->shift) {
            $shifts =$shifts->whereShift(\request()->shift);
        }
        $shifts = $shifts->select('shift')->first();
        $classes = Form::class()->whereShift($shifts->shift)->get();

        $forms = $classes->map(function ($data){
            return $data->name;
        });

        if (\request()->f) {


            $collections = array();
            foreach ( $classes as $class ) {
                $fees = Payment::whereHas('user',function ( $q ) use ($shifts, $class ) {
                    $q->active()->student()->whereHas('form',function ($q) use ($shifts, $class ) {
                        $q->whereId($class->id)->whereShift($shifts->shift);
                    });
                });

                if (\request()->s) {
                    $fees = $fees->whereDate('created_at','>=',\request()->s)->whereDate('created_at','<=',\request()->e);
                }

                if (\request()->p) {
                    $fees = $fees->whereFeeId(request()->p);
                }
                $fees = $fees->sum('amount');


                $collections[]=$fees;
            }




            return response()->json(['totals'=>$collections,'classes'=>$forms]);
        }

        if (\request()->r) {
            $males = [];
            $females = [];
            $maleTotals = 0;
            $femaleTotals = 0;
            foreach ( ['Male','Female'] as $gender ) {

                foreach ( $classes as $class ) {

                    $users = User::whereGender($gender)->whereHas('form',function ($q) use ( $class ) {
                        $q->whereId($class->id);
                    });
//                    if (\request()->t == 'today') {
//                        $users = $users->whereDate('enrolled',Carbon::today());
//                    }
//                    else {
//                        $users = $users->whereBetween('enrolled',[$start,$end]);
//                    }
                    $users = $users->student()->active()->count();

                    $gender == "Male"? $maleTotals += $users : $femaleTotals += $users;
                    $gender == "Male"? $males[] = $users : $females[] = $users;
                }

            }

            return response()->json(['malesTotal'=>$maleTotals,'femalesTotal'=>$femaleTotals,'males'=>$males,'females'=>$females,'classes'=>$forms]);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
