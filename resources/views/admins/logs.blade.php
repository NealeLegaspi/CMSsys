@extends('layouts.admin')
@section('title','Activity Logs')
@section('header')
    <i class="bi bi-journal-text me-2"></i> Activity Logs
@endsection

@section('content')
@include('partials.alerts')

<div class="container-fluid my-4">

  {{-- 🧭 Header Card --}}
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

      {{-- 🗂 Tabs --}}
      <ul class="nav nav-tabs mb-3" id="logsTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active fw-semibold" id="active-tab" data-bs-toggle="tab" data-bs-target="#activeLogs" type="button" role="tab">
            <i class="bi bi-activity me-1"></i> Active Logs
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link fw-semibold" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archivedLogs" type="button" role="tab">
            <i class="bi bi-archive me-1"></i> Archived Logs
          </button>
        </li>
      </ul>

      {{-- 📋 Tab Content --}}
      <div class="tab-content" id="logsTabContent">
        {{-- Active Logs --}}
        <div class="tab-pane fade show active" id="activeLogs" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
              <thead class="table-primary">
                <tr>
                  <th>User</th>
                  <th>Action</th>
                  <th>Date & Time</th>
                  <th>Operation</th>
                </tr>
              </thead>
              <tbody>
                @forelse($activeLogs as $log)
                  <tr>
                    <td>{{ optional($log->user)->email ?? 'System' }}</td>
                    <td class="text-start">{{ $log->action }}</td>
                    <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                      <form action="{{ route('admins.logs.archive', $log->id) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-warning">
                          <i class="bi bi-archive"></i> Archive
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                      <i class="bi bi-info-circle"></i> No active logs found.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-end mt-3">
            {{ $activeLogs->appends(request()->query())->links('pagination::bootstrap-5') }}
          </div>
        </div>

        {{-- Archived Logs --}}
        <div class="tab-pane fade" id="archivedLogs" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
              <thead class="table-secondary">
                <tr>
                  <th>User</th>
                  <th>Action</th>
                  <th>Date & Time</th>
                  <th>Operation</th>
                </tr>
              </thead>
              <tbody>
                @forelse($archivedLogs as $log)
                  <tr>
                    <td>{{ optional($log->user)->email ?? 'System' }}</td>
                    <td class="text-start">{{ $log->action }}</td>
                    <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                      <form action="{{ route('admins.logs.unarchive', $log->id) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-success">
                          <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                      <i class="bi bi-info-circle"></i> No archived logs found.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
            <div class="d-flex justify-content-end mb-3">
              <div class="alert alert-warning small py-2 px-3 mb-0 d-inline-flex align-items-center">
                <i class="bi bi-clock-history me-2"></i>
                Archived logs will be&nbsp;<strong>automatically deleted after 30 days</strong>.
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-3">
            {{ $archivedLogs->appends(request()->query())->links('pagination::bootstrap-5') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
