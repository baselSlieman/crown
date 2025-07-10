<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $categories = Category::with('products')->has('products')->get();
        $productsQuery = Product::query();
        $name = $request->input('name');
        $min = $request->input('min');
        $max = $request->input('max');
        $categoriesIds = $request->input('categories');
        if(!empty($name)){
           $productsQuery->where('name','like',"%{$name}%");
        }
        if(!empty($categoriesIds)){
            $productsQuery->whereIn('category_id',$categoriesIds);
         }
         if(!empty($min)){
            $productsQuery->where('price','>=',$min);
         }
         if(!empty($max)){
            $productsQuery->where('price','<=',$max);
         }
        //$products = $productsQuery->get();
        $products = $productsQuery->with('category')->paginate(5);
        return view("admin.product.index",compact('products','name','categories','min','max'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $product = new Product();
        $categories = Category::all();
        $product->fill(['quantity'=>0,'price'=>0]);
        return view('admin.product.create',compact('product','categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $formFileds = $request->validated();
        if($request->hasFile('image')){
            $formFileds['image']=$request->file('image')->store('product','public');
        }
        if(!$request->has('available')){
            $formFileds['available']=false;
        }
        Product::create($formFileds);
        return redirect()->route('products.index')->with('success','Product add successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view("admin.product.edit",compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->fill($request->validated())->save();
        return redirect()->route('products.index')->with('success','Product updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success','Product deleted successfully');
    }
}
