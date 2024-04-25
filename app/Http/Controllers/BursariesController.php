<?php

namespace App\Http\Controllers;

use App\Models\Bursary;
use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;

class BursariesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $bursaries = Bursary::select('name','slug','phone','registered','email','id')->withCount('user as total')->paginate();

        return inertia('Fees/Sponsorships',compact('bursaries'));
    }

    public function validation($request)
    {

        $rules =[
            'name'=>'required',
            'phone'=>'required',
            'email'=>'required|email',
            'registered'=>'required',
        ];

        $this->validate($request,$rules);
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

        Bursary::updateOrCreate([
            'name'=>$request->name,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'slug'=>str_slug($request->name),
            'registered'=>$request->registered,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bursary  $bursary
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show($bursary)
    {
        $bursary = Bursary::find($bursary);

        $users = User::whereHas('bursary',function ($q) use ($bursary) {
            $q->whereId($bursary->id);
        })
            ->select('fname','lname','gender','id')

            ->with('form:name,number')
            ->student()->active()
            ->paginate();
        $classes = Form::class()->get();

        return inertia('Fees/Sponsor',compact('users','bursary','classes'));
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
        $bursary->update([
            'name'=>$request->name,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'slug'=>str_slug($request->name),
            'registered'=>$request->registered,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bursary  $bursary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bursary $bursary)
    {
        $user = User::find($bursary);
        $user->bursary()->detach();
        $bursary->forceDelete();
    }
}
