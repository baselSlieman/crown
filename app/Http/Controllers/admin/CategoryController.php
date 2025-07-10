<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query()->paginate(5);
        return view("admin.category.index",compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = new Category();
        return view("admin.category.create",compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {

        $formFileds = $request->validated();
        if($request->hasFile('image')){
            $formFileds['image']=$request->file('image')->store('category','public');
        }
        Category::create($formFileds);
        return redirect()->route('categories.index')->with('success','category add successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $categoryProduts = $category->products()->get();
        return view('admin.category.show',compact('category','categoryProduts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view("admin.category.edit",compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->fill($request->validated())->save();
        return redirect()->route('categories.index')->with('success','category updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success','category delted successfully');
    }
}
