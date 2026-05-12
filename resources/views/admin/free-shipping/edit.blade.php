@extends('layouts.admin')
@section('title', 'Edit: ' . $freeShipping->name)
@section('page_title', 'Edit Free Shipping Rule')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.free-shipping.index') }}">Free Shipping</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<form action="{{ route('admin.free-shipping.update', $freeShipping) }}" method="POST">
    @csrf @method('PUT')
    @include('admin.free-shipping._form')
    <div class="d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('admin.free-shipping.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection