<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Reason;
use App\Models\School;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        if (request()->q ) {
            $users = User::search(request()->q)
                ->select(DB::raw('concat(fname," ",lname) as name'),'id')
                ->student()
                ->take(10)
                ->get();
            return response()->json($users);
        }

        $term = Term::status()->first();

        //get parents
        $users =  User::select('fname','lname','enrolled','student_id','id','gender')->with('form');
        $classes = Form::class()->get();

        if (\request()->u) {
            $users =  $users->search(\request()->s);
        }

        if ( \request()->f ) {
            $users = $users->whereHas( 'form', function ( $q ) use ($term) {
                $q->whereId( request()->f )->whereCalendarId($term->calendar_id);
            } );
        }

        if ( \request()->g ) {
            $users = $users->whereGender(request()->g);
        }

        if ( \request()->s ) {
            $users = $users->whereBetween('enrolled',[request()->s,request()->e]);
        }

        $users =  $users->student()->orderBy('lname')->orderBy('gender')->latest()->paginate(\request()->n);

        $users->setPath(\request()->fullUrl());

        $reasons = Reason::all();

        return inertia('Students/Registration',compact('users','classes','reasons'));
    }

    public function validation($request)
    {
        $fields =[
            'fname.required'=>'Field is mandatory',
            'lname.required'=>'Field is mandatory',
        ];
        $rules = [
            'fname'=>'required',
            'lname'=>'required',
            'enrolled'=>'required',
            'class'=>'required',
        ];
        $this->validate($request,$rules,$fields);
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

        $school = School::first();

        $id = explode(' ',$school->name)[0][0];

        $user =   User::updateOrcreate([
            'fname'=>$request->fname,
            'lname'=>$request->lname,
            'gender'=>$request->gender,
            'enrolled'=>$request->enrolled,
            'student_id'=>$id.'SS/'.random_int(1000,20000),
            'type'=>'Student',
        ]);

        $user->form()->attach($request->class);


        if ($request->hasFile('photo')) {
            $name =  $request->file('photo')->getClientOriginalName();

            $request->file('photo')->move(public_path('/img/'),$name);

            $user->update([
                'avatar'=>$name
            ]);
        }
        else {
            $user->update([
                'avatar'=>$request->gender == 'Male'?'boy.png':'girl.png',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show($user)
    {
        $user = User::profile()->with('guardian:id,fname,lname','bursary:id,name','form:id,name,number')->find($user);
       return inertia('Students/StudentPage',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validation($request);

        $user->update([
            'fname'=>$request->fname,
            'lname'=>$request->lname,
            'gender'=>$request->gender,
            'enrolled'=>$request->enrolled,
        ]);

        $user->form()->attach($request->class);

        if ($request->hasFile('photo')) {
            $name =  $request->file('photo')->getClientOriginalName();

            $request->file('photo')->move(public_path('/img/'),$name);

            $user->update([
                'avatar'=>$name
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
