<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use App\Models\Category;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Biography::updateOrCreate([
            'field_id'=>$request->f,
            'user_id'=>$request->id,
            'name'=>$request->value
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $profile
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(User $profile)
    {

        $categories = Category::whereType(strtolower($profile->type) ==='student'?'students':'staff')->with(['field'=>function($s) use ($profile) {
            $s->with(['biography'=>function($s) use ($profile) {
                $s->whereUserId($profile->id)->select('id','name','field_id');
            }]);
        }])->get();

        return inertia('Profile',compact('categories','profile'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Profile $profile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $profile)
    {
        if ($request->u){
            $meta = Profile::whereUserId($profile)->whereName($request->category)->first();
            $data = [];
            switch ($request->category) {
                case 'Sick':
                    $data = [
                        'reported'=>$request->reported,
                        'type'=>$request->type,
                        'status'=>$request->status
                    ];
                    break;
                case 'Transferred':
                case 'Special Needs':
                case 'Attrition':
                    $data = [
                        'reported'=>$request->reported,
                        'status'=>$request->status,
                    ];
                    break;
                case 'Leave':
                    $data = [
                        'reported'=>$request->reported,
                        'status'=>$request->status,
                        'back'=>$request->back,
                    ];
                    break;
            }

            if ($meta) {
                $meta->update([
                    'metas'=>$data
                ]);

            }else {
              $meta = Profile::updateOrCreate([
                   'name'=>$request->category,
                   'category'=>'user_profile',
                   'metas'=>$data,
                   'user_id'=>$profile
               ]);
            }

            $meta->user()->update([
                'status'=>0
            ]);

            return ;
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy($profile)
    {
        $profile = Profile::find($profile);
        $profile->user()->update([
            'status'=>1
        ]);

        $profile->delete();
    }
}
