@extends('layouts.admin')
@section('title', 'New Free Shipping Rule')
@section('page_title', 'New Free Shipping Rule')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.free-shipping.index') }}">Free Shipping</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<form action="{{ route('admin.free-shipping.store') }}" method="POST">
    @csrf
    @include('admin.free-shipping._form')
    <div class="d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-primary">Create Rule</button>
        <a href="{{ route('admin.free-shipping.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection