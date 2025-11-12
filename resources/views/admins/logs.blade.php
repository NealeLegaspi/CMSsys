@extends('layouts.admin')
@section('title','Activity Logs')
@section('header')
    <i class="bi bi-journal-text me-2"></i> Activity Logs
@endsection

@section('content')
@include('partials.alerts')

<div class="container-fluid my-4">

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      {{-- 🔍 Search & Filter --}}
      <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold">User</label>
          <select name="user_id" class="form-select">
            <option value="">All Users</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}" {{ request('user_id')==$user->id ? 'selected' : '' }}>
                {{ $user->email }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold">Action</label>
          <input type="text" name="action" value="{{ request('action') }}" class="form-control" placeholder="Search action...">
        </div>

        <div class="col-md-2">
          <label class="form-label fw-semibold">From</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>

        <div class="col-md-2">
          <label class="form-label fw-semibold">To</label>
          <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>

        <div class="col-md-2 d-flex gap-2">
          <button class="btn btn-outline-primary">
            <i class="bi bi-search"></i> Search
          </button>
          <a href="{{ route('admins.logs') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </a>
        </div>
      </form>

      {{-- 📋 Logs Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
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
                <td class="text-center">{{ $log->action }}</td>
                <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-3">
                  <i class="bi bi-info-circle"></i> No logs found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-end mt-3">
        {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>
@endsection
