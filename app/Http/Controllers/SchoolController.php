<?php

namespace App\Http\Controllers;

use App\Models\FormMaker;
use App\Models\School;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic;

class SchoolController extends Controller
{
    private $successStatus = 200;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $school = School::withCount(['students'=>function ($q){
            $q->status(1);
        }])->withCount(['teachers'=>function ($q){
            $q->status(1);
        }])->withCount(['parents'=>function ($q){
            $q->status(1);
        }])->with('subject')->first();
        return response()->json($school,200);
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
            'name'=>'sometimes|required|min:4|bail|string',
            'email'=>'sometimes|required|email|bail|string',
            'motto'=>'sometimes|required|min:4|bail|string',
            'phone'=>'sometimes|required|min:4|bail|string',
            'address'=>'sometimes|required|min:4|bail|string',
            'photo'=>'sometimes|image',

        ]);


        if ($request->cat) {
        	Setting::updateOrCreate([
        		'category'=>$request->cat,
        		'name'=>$request->cat,
        		'value'=>['body'=>$request->body],
	        ]);

        	return;
        }

        if ($request->q) {
        	Setting::updateOrCreate([
        		'category'=>$request->q,
        		'name'=>$request->title,
        		'value'=>['answer'=>$request->description],
	        ]);

        	return;
        }

        if ($request->t) {
        	$setting = Setting::updateOrCreate([
        		'category'=>$request->t,
        		'name'=>$request->name,
	        ]);
        	if ($request->hasFile('photo')) {
        		$name = $request->file('photo')->getClientOriginalName();
		        $request->file('photo')->move(public_path('img'),$name);
		        $setting->update([
			        'value'=>['logo'=>$name],
		        ]);
	        }

        	return;
        }

        if ($request->s) {

	        $profile =[
		        'name'=>$request->name,
		        'email'=>$request->email,
		        'motto'=>$request->motto,
		        'phone'=>$request->phone,
		        'address'=>$request->address,
		        'slug'=>str_slug($request->name)
	        ];

	        if ($request->hasFile('photo')) {

		        $name = $request->file('photo')->getClientOriginalName();
		        $request->file('photo')->move(public_path('img'),$name);

		        $profile['logo']= $name;

	        }

	        if ($school = School::first()) {
		        $school->update([
			        'about'=> $profile,
			        'aims'=>$request->aims,
			        'biography'=>$request->biography,
			        'user_id'=>Auth::id()
		        ]);
	        }
	        else {
		      $school =  School::updateOrCreate([
			        'about'=> $profile,
			        'aims'=>$request->aims,
			        'biography'=>$request->biography,
			        'user_id'=>Auth::id()
		        ]);
	        }

	        Auth::user()->update([
		        'school_id'=>$school->id
	        ]);

	        return;
        }



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($school)
    {
    	if ($school == 'profile') {
		    $school = School::first();
		    return response()->json($school,200);
	    }
	    $category =Setting::whereCategory($school);

    	if (\request()->c) {
		    $category = $category->paginate();

		    return response()->json($category,200);
	    }

	    $category =$category->whereName($school)->first();

	    return response()->json($category,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $school)
    {
	    if ($request->t) {
		    $setting = Setting::find($school);

		    $setting->update([
			    'name'=>$request->name,
		    ]);

		    if ($request->hasFile('photo')) {
			    $file = public_path('img').$setting->value['logo'] ;
			    if (file_exists($file)) unlink($file);

			    $name = $request->file('photo')->getClientOriginalName();
			    $request->file('photo')->move(public_path('img'),$name);
			    $setting->update([
				    'value'=>['logo'=>$name],
			    ]);
		    }


		    return;
	    }

	    if ($request->q) {
		    $setting = Setting::find($school);

		    $setting->update([
			    'name'=>$request->title,
			    'value'=>['answer'=>$request->description],

		    ]);

		    return;
	    }


      $school->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'slug'=>str_slug($request->name),
            'motto'=>$request->motto,
            'phone'=>$request->phone,
            'biography'=>$request->biography,
            'address'=>$request->address,
        ]);

        if ($request->photo) {
            ImageManagerStatic::make($request->cover)->save(public_path('img/'.$request->photo));

            $school->update([
                'logo'=>$request->photo,
            ]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	$setting = Setting::find($id);
    	if (\request()->t) {
    		$file = public_path('img').$setting->value['logo'] ;
    		if (file_exists($file)) unlink($file);
	    }

    	$setting->delete();

    }
}
