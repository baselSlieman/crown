@extends('layouts.app')

@section("title",$category->name." Products")

@section('content')

<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Categories List</p>
                    <h1>@yield('title')</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5 py-3">
    <a href="{{route('categories.index')}}" class="btn btn-primary">go back</a>

    @if (session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
@endif

<table class="table mt-3">
    <thead>
        <tr>
            <th>id</th>
            <th>image</th>
            <th>name</th>
            <th>description</th>
            <th>option</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categoryProduts as $catProduct)
        <tr>
            <td>{{$catProduct->id}}</td>
            <td><img src="/storage/{{$catProduct->image}}" width="100"/></td>
            <td>{{$catProduct->name}}</td>
            <td>{{$catProduct->description}}</td>
            <td>
                <div class="btn-group">
                    <a href="{{route('products.edit',$catProduct)}}" class="btn btn-primary">Edit</a>

                </div>
            </td>
        </tr>
        @empty
            <tr><td colspan="5" class="text-center"><h3>No Category</h3></td></tr>
        @endforelse

    </tbody>
</table>
</div>
@endsection
