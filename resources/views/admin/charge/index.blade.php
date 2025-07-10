@extends('admin.layouts.app')

@section('title', 'Charges')

@section('content')




    <div class="container my-3">
        <div class="row justify-content-center g-2 gx-3">
            @livewire('Charge',["chat_id"=>$chat_id])
    </div>
    </div>


@endsection
