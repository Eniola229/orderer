@extends('layouts.admin')
@section('title', 'Edit Newsletter')
@section('page_title', 'Edit Newsletter')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.newsletter.index') }}">Newsletter</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Draft — {{ $newsletter->subject }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.newsletter.update', $newsletter) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.newsletter._form', ['newsletter' => $newsletter])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-1"></i> Update Draft
                        </button>
                        <a href="{{ route('admin.newsletter.show', $newsletter) }}"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection