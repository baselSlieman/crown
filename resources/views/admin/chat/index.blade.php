@extends('admin.layouts.app')

@section('title', 'Chats')

@section('content')



    <div class="container my-3">
        <div class="row justify-content-center g-2 gx-3">
            @livewire('Chat')

    </div>
    </div>


@endsection
