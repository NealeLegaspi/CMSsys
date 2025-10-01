@extends('layouts.admin')
@section('title','Activity Logs')
@section('header','Activity Logs')

@section('content')
@include('partials.alerts')

<div class="card shadow-sm">
  <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
    📜 Activity Logs
  </div>
  <div class="card-body">
    <!-- Search & Filter -->
    <form method="GET" class="row g-2 mb-3">
      <div class="col-md-3">
        <select name="user_id" class="form-select form-select-sm">
          <option value="">All Users</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}" {{ request('user_id')==$user->id?'selected':'' }}>
              {{ $user->email }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <input type="text" name="action" value="{{ request('action') }}" class="form-control form-control-sm" placeholder="Search action...">
      </div>
      <div class="col-md-2">
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
      </div>
      <div class="col-md-2">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
      </div>
      <div class="col-md-2 d-flex">
        <button class="btn btn-sm btn-primary me-2">Filter</button>
        <a href="{{ route('admins.logs') }}" class="btn btn-sm btn-secondary">Clear</a>
      </div>
    </form>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="logsTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#activeLogs" type="button" role="tab">Active Logs</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archivedLogs" type="button" role="tab">Archived Logs</button>
      </li>
    </ul>

    <div class="tab-content" id="logsTabContent">
      <!-- Active Logs -->
      <div class="tab-pane fade show active" id="activeLogs" role="tabpanel">
        <table class="table table-bordered table-striped">
          <thead class="table-primary">
            <tr>
              <th>User</th>
              <th>Action</th>
              <th>Date & Time</th>
              <th width="150">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($activeLogs as $log)
            <tr>
              <td>{{ optional($log->user)->email ?? 'System' }}</td>
              <td>{{ $log->action }}</td>
              <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
              <td>
                <form action="{{ route('admins.logs.archive', $log->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn btn-sm btn-warning">Archive</button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted">No active logs found.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="mt-3">{{ $activeLogs->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
      </div>

      <!-- Archived Logs -->
      <div class="tab-pane fade" id="archivedLogs" role="tabpanel">
        <table class="table table-bordered table-striped">
          <thead class="table-secondary">
            <tr>
              <th>User</th>
              <th>Action</th>
              <th>Date & Time</th>
              <th width="150">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($archivedLogs as $log)
            <tr>
              <td>{{ optional($log->user)->email ?? 'System' }}</td>
              <td>{{ $log->action }}</td>
              <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
              <td>
                <form action="{{ route('admins.logs.unarchive', $log->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn btn-sm btn-success">Unarchive</button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted">No archived logs found.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="mt-3">{{ $archivedLogs->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
