@extends('layouts.registrar')

@section('title', 'Quarter Control')
@section('header')
    <i class="bi bi-calendar-gear me-2"></i> Quarter Control Panel
@endsection

@section('content')
<div class="container-fluid">

    @include('partials.alerts')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex align-items-start mb-4">
                <div class="me-3">
                    <span class="badge bg-primary p-3 fs-5 rounded-3 shadow-sm">
                        <i class="bi bi-clock-history"></i>
                    </span>
                </div>
                <div>
                    <h5 class="fw-semibold mb-1">Current Active Quarter</h5>
                    <p class="text-muted mb-0">
                        The active quarter controls which grading period is <strong>open for teachers</strong>. 
                        Future quarters remain locked until activated.
                    </p>
                </div>
            </div>

            <form id="quarterForm" method="POST" action="{{ route('registrars.updateQuarter') }}" class="row g-3">
                @csrf

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Select Quarter</label>
                    <select name="quarter" class="form-select form-select-lg shadow-sm" required>
                        @for($q = 1; $q <= 4; $q++)
                            <option value="{{ $q }}" {{ $activeQuarter == $q ? 'selected' : '' }}>
                                Quarter {{ $q }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-primary btn-lg w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#confirmQuarterModal">
                        <i class="bi bi-save me-2"></i> Update Quarter
                    </button>
                </div>
            </form>

        </div>
    </div>

    <div class="alert alert-secondary border-0 shadow-sm d-flex align-items-center small">
        <i class="bi bi-shield-lock fs-5 me-2"></i>
        <div>
            Teachers can only encode grades up to <strong>Quarter {{ $activeQuarter }}</strong>. 
            All later quarters are automatically locked.
        </div>
    </div>

</div>

{{-- ✅ Confirmation Modal --}}
<div class="modal fade" id="confirmQuarterModal" tabindex="-1" aria-labelledby="confirmQuarterLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-primary text-white rounded-top-3">
        <h5 class="modal-title" id="confirmQuarterLabel">
          <i class="bi bi-exclamation-triangle me-2"></i> Confirm Quarter Change
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        You are about to change the <strong>Active Quarter</strong>.  
        This will immediately affect which grading period teachers can edit.
        <br><br>
        <span class="text-danger fw-semibold">Are you sure you want to continue?</span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('quarterForm').submit();">
          Yes, Proceed
        </button>
      </div>
    </div>
  </div>
</div>
@endsection
