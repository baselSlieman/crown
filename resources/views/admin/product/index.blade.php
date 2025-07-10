@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')



    <div class="container my-3">
        <div class="row justify-content-center g-2 gx-3">

            <div class="col col-md-9">

                <div class="d-flex justify-content-between align-items-center">
                    <h1>@yield('title')</h1>
                    <div><a href="{{ route('products.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i></a></div>
                </div>

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
                            <th>quantity</th>
                            <th>price</th>
                            <th>available</th>
                            <th>category</th>
                            <th>option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td><img src="storage/{{ $product->image }}" width="100" /></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->description }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->available }}</td>
                                <td>
                                    <span class="badge rounded-pill text-bg-primary bg-primary">
                                        <a class="text-light"
                                            href="{{ route('categories.show', $product->category) }}">{{ $product->category->name }}</a>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i
                                                class="fa fa-edit"></i></a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i
                                                    class="fa fa-trash"></i></button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <h3>No Product</h3>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>


                {{ $products->links() }}
            </div>
            <div class="col col-md-3 p-3">
                <h4>Filter</h4>
                <form method="GET">
                    <div class="form-group mb-2">
                        <label for="name">Name or Description</label>
                        <input type="text" name="name" id="name" value="{{ $name }}"
                            class="form-control" placeholder="name" aria-describedby="helpId" />
                    </div>
                    <h5 class="mb-0 mt-3">Categories</h5>

                    @foreach ($categories as $key =>$category)
                        <div class="form-check">
                            <input id="cat-{{$key}}" @checked(in_array($category->id, Request::input('categories') ?? [])) type="checkbox" name="categories[]"
                                value="{{ $category->id }}" class="form-check-input" />
                            <label for="cat-{{$key}}" class="form-check-label">{{ $category->name }}</label>
                        </div>
                    @endforeach
                    <h5 class="mb-0 mt-3">Pricing</h5>
                    <div class="form-group mb-2">
                        <label for="name">Min</label>
                        <input type="number" name="min" id="min" value="{{$min}}"
                            class="form-control" placeholder="min" aria-describedby="helpId" />
                    </div>
                    <div class="form-group mb-1">
                        <label for="max">Max</label>
                        <input type="number" name="max" id="max" value="{{$max}}"
                            class="form-control" placeholder="max" aria-describedby="helpId" />
                    </div>


                    <div class="form-group mb-2 mt-3">
                        <input type="submit" class="btn btn-primary" value="filter" />
                        <a class="btn btn-secondary" href="{{route('products.index')}}">Reset</a>
                    </div>
                </form>


        </div>
    </div>
    </div>
@endsection
