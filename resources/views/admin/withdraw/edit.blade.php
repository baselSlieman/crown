@extends('admin.layouts.app')

@section('title', 'Edit Withdraw')

@section('content')

    @livewire('WithdrawsEdit',["withdraw"=>$withdraw])
@endsection
