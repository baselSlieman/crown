@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Categories</p>
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
        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="" value="{{old('name')}}"
                    aria-describedby="helpId" />
                <small id="helpId" class="text-muted">Help text</small>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3">{{old('description')}}</textarea>
            </div>



            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" name="image" id="image" placeholder="image" value="{{old('image')}}"
                    aria-describedby="fileHelpId" />
                <div id="fileHelpId" class="form-text">Help text</div>
            </div>

            <input type="submit" class="btn btn-primary" value="Add"/>



        </from>
    </div>

@endsection
