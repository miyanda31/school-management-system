<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Profile;
use App\Models\Reason;
use App\Models\Role;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
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
                ->teacher()
                ->active()
                ->take(10)
                ->get();
            return response()->json($users);
        }

        //get parents
        $users =  User::select('fname','lname','phone','student_id','id','gender','email','role_id')->with('role');

        if (\request()->s && \request()->s != 'Active') {
            $users->with('profiles:id,user_id,name,value,metas')->whereHas('profiles',function ($s){
                $s->whereName(\request()->s)->whereCategory('user_profile');
            });
        }

        if (\request()->u) {
            $users =  $users->search(\request()->u);
        }

        if ( \request()->g ) {
            $users = $users->whereGender(request()->g);
        }

        if ( \request()->r ) {
            $users = $users->whereRoleId(request()->r);
        }

        $users =  $users->adminOrTeacher()->orderBy('lname')->orderBy('gender')->latest()->paginate(\request()->n);

        $users->setPath(\request()->fullUrl());

        $reasons = Reason::all();
        $roles = Role::all();

        return inertia('Staff/Registration',compact('users','reasons','roles'));
    }

    public function validation($request)
    {
        $fields =[
            'required'=>'Field is mandatory',

        ];
        $rules =[
            'fname'=>'required',
            'lname'=>'required',
            'phone'=>'required',
            'number'=>'required',
            'role'=>'required',

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

       $user = User::updateOrcreate([
            'fname'=>$request->fname,
            'lname'=>$request->lname,
            'gender'=>$request->gender,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'student_id'=>$request->number,
            'type'=>'Teacher',
            'role_id'=>$request->role,
        ]);


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
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user)
    {

        $this->validation($request);

        $user = User::find($user);

        $user->update([
            'fname'=>$request->fname,
            'lname'=>$request->lname,
            'gender'=>$request->gender,
            'email'=>$request->email,
            'phone'=>$request->phone,

        ]);

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
