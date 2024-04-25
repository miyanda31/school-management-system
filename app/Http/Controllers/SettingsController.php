<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Category;
use App\Models\Code;
use App\Models\Form;
use App\Models\Grading;
use App\Models\Setting;
use App\Models\Subject;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected function validation(Request $request) {
        $rules =[
            'name'=>'sometimes|required',
        ];
        $messages =[
            'required'=>'This field is required'
        ];
        $this->validate($request,$rules,$messages);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        return inertia('Settings/Index');
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

        Category::updateOrCreate([
            'type'=>$request->type,
            'name'=> $request->name,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show($id)
    {
        $page = '';
        $data = [];
        switch ($id) {
            case 'subjects':
                $subjects = Subject::has('schools')->where('name','<>','ENGLISH')->get();
                $remaining = Subject::doesntHave('schools')->where('name','<>','ENGLISH')->get();
                $data = compact('subjects','remaining');
                $page = 'Subjects';
                break;
            case 'classes':
                $classes = Form::withCount('user as students')->paginate();
                $data = compact('classes');
                $page = 'Classes';
                break;
            case 'grading':
                $page = 'Grading';
                break;
            case 'calendar':
                $calendar = Calendar::withCount('term as terms')->get();
                $data = compact('calendar');
                $page = 'Calendar';
                break;
            case 'codes':
                $classes = Form::class()->get();
                $codes = Code::with('user:id,fname,lname,type')->has('user');

                if (request()->t) {
                    if (\request()->t > 0) {
                        $codes = $codes->whereHas('user',function ($s)  {
                            $s->student()->active()->whereHas('form',function ($s)  {
                                $s->whereId(request()->t);
                            });
                        });
                    }
                    else
                    $codes = $codes->whereHas('user',function ($s)  {
                        $s->where('type','<>','Student')->active()->whereType(request()->t);
                    });
                }

                $codes = $codes->paginate();

                $codes->setPath(\request()->fullUrl());
                $data = compact('codes','classes');
                $page = 'Codes';
                break;
            case 'promotion':
                $classes = Form::class()->withCount('user as total')->get();
                $years = Calendar::select('id','academic')->whereStatus(0)->get();
                $data = compact('classes','years');
                $page = 'Promotion';
                break;
            case 'staff':
            case 'parents':
            case 'students':
                $categories = Category::whereType($id)->withCount('field as fields')->paginate();
                $data = compact('categories','id');
                $page = 'Users';
                break;
        }

        return inertia('Settings/'.$page,$data);
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
        $this->validation($request);

        $setting = Category::find($id);
        $setting->update([
            'name'=> $request->name,
        ]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $setting = Category::find($id);
        $setting->field()->delete();
        $setting->delete();
    }
}
