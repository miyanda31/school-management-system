<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Field;
use Illuminate\Http\Request;

class CategoriesController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Field::updateOrCreate([
            'category_id'=>$request->category,
            'name'=> $request->name,
            'value'=>$request->values,
            'type'=>$request->type,
            'required'=>$request->required?1:0,

        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(Category $category)
    {
        $fields = $category->field()->get();
        return inertia('Settings/Fields',compact('fields','category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Field  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Field $category)
    {
        $category->update([
            'name'=> $request->name,
            'value'=>$request->values,
            'required'=>$request->required?1:0,
            'type'=>$request->type,
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
