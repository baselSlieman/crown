@extends('admin.layouts.app')

@section('title', 'User gifts')

@section('content')


@livewire('Gift',['chat'=>$chat])
@endsection
