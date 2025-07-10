@extends('admin.layouts.app')

@section('title', 'Send Message')

@section('content')

@livewire('Message',['chat'=>$chat])
@endsection
