@extends('admin.layouts.app')

@section('title', 'Edit Charge')

@section('content')

@livewire('ChargesEdit',['charge'=>$charge])
@endsection
