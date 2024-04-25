<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Fee;
use App\Models\Form;
use App\Models\Payment;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $years = Calendar::select('id','academic')->get();
        $fees = Fee::select('id','name')->get();


        $payment = Fee::select('id','name')->withCount(['package as total'=>function($s){
            $s->select(DB::raw('sum(amount)'));
        }])->with(['form'=>function($s){
            $s->class();
        }]);

        $payment = \request()->p ? $payment->find(\request()->p) : $payment->first();

        $term =\request()->y?Term::whereCalendarId(request()->y)->whereNumber(request()->t)->first():Term::status()->first();

        $collections = User::student()->active()->doesntHave('bursary')->select('fname','lname','gender','id','student_id')->with('form:id,name,number')->with('recently:created_at,user_id')
            ->withCount(['payment as paid'=>function($query) use ( $payment, $term ) {
                $query->select(DB::raw('SUM(amount)') )->groupBy('user_id')->whereFeeId($payment->id)->whereTermId($term->id);

            }])
            ->withCount(['payment as balance'=>function($q) use ( $term, $payment) {
                $q->select(DB::raw($payment->total.' - SUM(amount)'))->whereFeeId($payment->id)->whereTermId($term->id);
            }]) ;


            if (\request()->p) {
                $collections = $collections->whereHas('form',function ($q) {
                    $q->whereHas('fee',function ($s) {
                        $s->whereId(request()->p);
                    });
                });
            }

            if (\request()->f) {
                $collections = $collections->whereHas('form',function ($q) use ( $term ) {
                    $q->whereId(request()->f)->whereCalendarId($term->calendar_id);
                });
            }

            if (\request()->g) {
                $collections = $collections->whereGender(request()->g);
            }

            if (request()->s) {
                $collections = $collections->search(request()->n);
            }

        if (\request()->c) {

            if (\request()->c == 'Paid') {
                $collections = $collections->whereHas('payment',function ($q) use ( $payment, $term ) {
                    $q->whereTermId($term->id)->whereFeeId($payment->id)->groupBy('user_id') ->havingRaw( $payment->total.' - sum(amount) = 0 ' );
                }) ;
            }
            else if(\request()->c == 'Balances') {
                $collections = $collections->whereHas('payment',function ($q) use ( $payment, $term ) {
                    $q->whereTermId($term->id)->whereFeeId($payment->id)->groupBy('user_id') ->havingRaw( $payment->total.' - sum(amount) > 0 ' );
                });
            }
            else {
                $collections = $collections->orDoesntHave('payment') ;
            }

        }

           $collections =   $collections->paginate(\request()->n);
            $collections->setPath(request()->fullUrl());

        return inertia('Fees/Payments',compact('collections','term','payment','fees','years'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'amount'=>'required',
            'receipt'=>'required',
        ]);


        $term = Term::status()->first();

        Payment::updateOrCreate([
            'amount'=>$request->amount,
            'receipt'=>$request->receipt,
            'fee_id'=>$request->fee,
            'term_id'=>$term->id,
            'user_id'=>$request->id,
        ]);



    }

    /**
     * Display the specified resource.
     *
     * @param  int $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(int $user)
    {
        $current =\request()->t?Term::with('calendar:id,academic')
            ->where('calendar_id',request()->y)
            ->where('number',request()->t)
            ->first():
            Term::status()->first();

        if ($current) {

            $user = User::select('fname','lname','gender','id','student_id','avatar')->with('form:id,name,number','guardian:id,fname,lname,phone')
                ->with(['payment'=>function($query) use ( $current ) {
                    $query->select('amount','receipt','id','created_at','user_id')->whereFeeId(\request()->p)->whereTermId($current->id);
                }])
                ->withCount(['payment as paid'=>function($query) use ( $current ) {
                    $query->select(DB::raw('SUM(amount)') )->groupBy('user_id')->whereFeeId(\request()->p)->whereTermId($current->id);

                }])
                ->find($user);

            $payment = Fee::withCount(['package as total'=>function($query)  {
                $query->select(DB::raw('SUM(amount)'));

            }])->find(\request()->p);

            $ids = DB::table('form_user')->whereUserId($user->id)->pluck('calendar_id')->toArray();

            $years = Calendar::whereIn('id',$ids)->select('id','academic')->get();


            return inertia('Fees/Profile',compact('user','payment','years','current'));
        }
        else {
            return redirect()->back()->withErrors(['term'=>'No data was found for the selected term and year']);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
