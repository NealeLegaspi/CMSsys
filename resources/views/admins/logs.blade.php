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
          <th>#</th>
          <th>User</th>
          <th>Action</th>
          <th>IP</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $i => $log)
        <tr>
          <td>{{ $logs->firstItem() + $i }}</td>
          <td>{{ optional($log->user)->email ?? 'System' }}</td>
          <td>{{ $log->action }}</td>
          <td>{{ $log->ip_address }}</td>
          <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted">No activity yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-3">{{ $logs->links('pagination::bootstrap-5') }}</div>
  </div>
</div>
@endsection
