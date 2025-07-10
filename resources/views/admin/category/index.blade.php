@extends('layouts.app')

@section("title","Categories")

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
    <a href="{{route('categories.create')}}" class="btn btn-primary">Create new</a>

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
        @forelse($categories as $category)
        <tr>
            <td>{{$category->id}}</td>
            <td><img src="storage/{{$category->image}}" width="100"/></td>
            <td>{{$category->name}}</td>
            <td>{{$category->description}}</td>
            <td>
                <div class="btn-group">
                    <a href="{{route('categories.edit',$category)}}" class="btn btn-primary">Edit</a>
                    <a href="{{route('categories.show',$category)}}" class="btn btn-secondary">Show</a>
                    <form method="POST" action="{{route('categories.destroy',$category)}}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger mx-1">Delete</button>
                    </form>

                </div>
            </td>
        </tr>
        @empty
            <tr><td colspan="5" class="text-center"><h3>No Category</h3></td></tr>
        @endforelse

    </tbody>
</table>
{{$categories->links()}}
</div>
@endsection
