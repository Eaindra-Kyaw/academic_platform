{{-- resources/views/student/notifications.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifications')
@section('role', 'Student')
@section('page-title', '🔔 Notifications')
@section('welcome-text', 'Your system notifications')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5>Notifications</h5>
                <p class="text-muted">Notifications feature coming soon...</p>
            </div>
        </div>
    </div>
@endsection
