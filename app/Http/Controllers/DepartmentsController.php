<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Event;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentsController extends Controller
{
    public function validation($request) {
        $messages =[
            'required'=>'Field is required'
        ];
        $this->validate($request,[
            'name'=>'required',
            'description'=>'required',
            'members'=>'required',
        ],$messages);

    }


    public function index()
    {
        $departments  = Department::with(['user'=>function($s){
            $s->select(DB::raw('concat(lname," ",fname) as name'),'id','role_id');
        }]);

        if (\request()->p) {
            $departments = $departments->whereHas('user',function ( $q ){
                $q->whereId(Auth::id());
            })->get();

            return response()->json($departments);
        }

        $departments = $departments->get();


        $users =User::select(DB::raw('concat(fname," ",lname) as name'),'id')
            ->adminOrTeacher()
            ->active()
            ->whereDoesntHave('department')
            ->get();

        return inertia('Departments/Departments',compact('departments','users'));
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

        $department = Department::updateOrCreate([
            'name'=>$request->name,
            'description'=>$request->description,
        ]);

        $department->user()->attach($request->members);
        $department->user()->attach($request->hods);

        $users = User::whereIn('id',$request->hods)->get();

        foreach ($users as $item) {
            $item->update([
                'role_id'=>6
            ]);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param Department $department
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(Department $department)
    {

        $term = Term::status()->first();
        $users = User::adminOrTeacher()->fullname()->get();

       $members = User::whereHas('department',function ($s) use ($department) {
            $s->whereId($department->id);
        })->select('id','fname','lname','role_id')->with('role')->withCount(['allocation as load'=>function($s) use ($term) {
            $s->withCount('timetable as total')->whereTermId($term->id);
        }])->get();

        $activities = Event::whereHas('activities',function ($s) use ($department) {
            $s->whereId($department->id);
        })->select('start','title','status','id','created_at','end')->whereTermId($term->id)->take(10)->latest()->get();
        $tasks = Event::whereHas('tasks',function ($s) use ($department) {
            $s->whereId($department->id);
        })->select('start','title','status','id','created_at','end')->whereTermId($term->id)->take(10)->latest()->get();

        return inertia('Departments/Department',compact('activities','tasks','department','users','members'));

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $this->validation($request);

        $department->update([
            'name'=>$request->name,
            'description'=>$request->description
        ]);

        $department->user()->detach();
        $department->user()->update([
            'role_id'=>null
        ]);

        $department->user()->attach($request->members);
        $department->user()->attach($request->hods);


        $users = User::whereIn('id',$request->hods)->get();

        foreach ($users as $item) {
            $item->update([
                'role_id'=>5
            ]);

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        if (\request()->u) {
            $department->user()->detach(\request()->u);
            return ;
        }
        $department->user()->detach();
        $department->delete();
    }
}
