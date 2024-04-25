<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        if (request()->s ) {
            $users = User::search(request()->s)
                ->select(DB::raw('concat(fname," ",lname) as name'),'id')
                ->parent()
                ->take(10)
                ->get();
            return response()->json($users);
        }

        //get parents
        $users =  User::select('fname','lname','phone','email','id','gender')
            ->withCount('ward as wards')->with('ward:id,fname,lname');

        if (\request()->s) {
            $users =  $users->search(\request()->s);
        }

        $users =  $users->parent()->latest()->paginate(15);

        return inertia('Parents/Parents',compact('users'));
    }

    public function validation($request)
    {
        $fields =[
            'fname.required'=>'Field is mandatory',
            'lname.required'=>'Field is mandatory',
            'phone.required'=>'Field is mandatory',

        ];
        $rules =[
            'fname'=>'sometimes|required',
            'lname'=>'sometimes|required',
            'phone'=>'sometimes|required',

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

        $user =   User::updateOrcreate([
            'fname'=>$request->fname,
            'lname'=>$request->lname,
            'gender'=>$request->gender,
            'email'=>$request->email,
            'avatar'=>$request->gender == 'Male'?'boy.png':'girl.png',
            'phone'=>$request->phone,
            'type'=>'Parent',
        ]);

        $user->ward()->attach($request->wards);

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

        if (count($request->ward)>0)
        $user->ward()->sync($request->ward);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($user)
    {
        $user = User::find($user);
        $user->ward()->detach();
        $user->delete();
    }
}
