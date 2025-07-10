@extends('admin.layouts.app')

@section('title', 'User Affiliates')

@section('content')

@livewire('Affiliate',['chat'=>$chat])
@endsection
