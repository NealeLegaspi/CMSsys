@extends('layouts.admin')
@section('title','Activity Logs')
@section('header','Activity Logs')

@section('content')
@include('partials.alerts')

<div class="card shadow-sm">
  <div class="card-header bg-light fw-bold">📜 Activity Logs</div>
  <div class="card-body">
    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>User</th>
          <th>Action</th>
          <th>Date & Time</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td>{{ optional($log->user)->email ?? 'System' }}</td>
          <td>{{ $log->action }}</td>
          <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
        </tr>
        @empty
        <tr><td colspan="3" class="text-center text-muted">No activity logs found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-3">{{ $logs->links('pagination::bootstrap-5') }}</div>
  </div>
</div>
@endsection
