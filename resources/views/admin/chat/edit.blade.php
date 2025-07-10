@extends('admin.layouts.app')

@section('title', 'Edit Chat')

@section('content')

@livewire('Edit',['chat'=>$chat])

@endsection
