<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    protected function validation(Request $request) {
        $rules =[
            'amount'=>'sometimes|required',
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
        $packages = Package::paginate();
        return inertia('Fees/Packages',compact('packages'));
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

        Package::updateOrCreate([
            'name'=> $request->name,
            'amount'=> $request->amount,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function show(Package $package)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Package $package)
    {

        $this->validation($request);

        $package->update([
            'name'=> $request->name,
            'amount'=> $request->amount,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function destroy(Package $package)
    {

        $package->payment()->detach();
        $package->delete();
    }
}
