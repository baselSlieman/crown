@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Products</p>
                        <h1>@yield('title')</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container my-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('products.update',$product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder=""
                    value="{{ old('name',$product->name) }}" aria-describedby="helpId" />
                <small id="helpId" class="text-muted">Help text</small>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3">{{ old('description',$product->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <textarea class="form-control" name="quantity" id="quantity" rows="3">{{ old('quantity',$product->quantity) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <textarea class="form-control" name="price" id="price" rows="3">{{ old('price',$product->price) }}</textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" @checked($product->available) name="available" type="checkbox" value="1" id="available" />
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
                        <option @selected(old('category_id',$product->category_id)===$category->id) value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach


                </select>
            </div>

            <input type="submit" class="btn btn-primary" value="Save" />



            </from>
    </div>

@endsection
