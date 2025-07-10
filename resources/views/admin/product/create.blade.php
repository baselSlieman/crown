@extends('admin.layouts.app')

@section('title', 'Create Product')

@section('content')

    <div class="container my-3">
        <h1>@yield('title')</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder=""
                    value="{{ old('name') }}" aria-describedby="helpId" />
                <small id="helpId" class="text-muted">Help text</small>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <textarea class="form-control" name="quantity" id="quantity" rows="3">{{ old('quantity') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <textarea class="form-control" name="price" id="price" rows="3">{{ old('price') }}</textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" name="available" type="checkbox" value="1" id="available" />
                <label class="form-check-label" for="available"> available? </label>
            </div>


            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" name="image" id="image" placeholder="image"
                    aria-describedby="fileHelpId" />
                <div id="fileHelpId" class="form-text">Help text</div>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select form-select-lg form-control" name="category_id" id="category_id">
                    @foreach ($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach


                </select>
            </div>

            <input type="submit" class="btn btn-primary" value="Add" />



            </from>
    </div>

@endsection
