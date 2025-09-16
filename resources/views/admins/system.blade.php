@extends('layouts.admin') 
{{-- Gumamit ng iyong admin layout --}}

@section('title','System Settings')
@section('header','System Settings')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-sliders me-2"></i> System Settings</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admins.system.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          {{-- School Logo --}}
          <div class="mb-4 text-center">
            <label class="form-label fw-bold">School Logo</label>
            <div class="mb-2">
              <img src="{{ isset($settings['school_logo']) ? asset('storage/'.$settings['school_logo']) : asset('images/default-logo.png') }}" 
                   alt="School Logo" class="rounded shadow-sm" style="height: 120px; object-fit: contain;">
            </div>
            <input type="file" name="school_logo" class="form-control @error('school_logo') is-invalid @enderror">
            @error('school_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- School Name --}}
          <div class="mb-3">
            <label class="form-label fw-bold">School Name</label>
            <input type="text" name="school_name" 
                   class="form-control @error('school_name') is-invalid @enderror" 
                   value="{{ old('school_name', $settings['school_name'] ?? '') }}" required>
            @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Address --}}
          <div class="mb-3">
            <label class="form-label fw-bold">School Address</label>
            <textarea name="school_address" rows="2" 
                      class="form-control @error('school_address') is-invalid @enderror" required>{{ old('school_address', $settings['school_address'] ?? '') }}</textarea>
            @error('school_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Contact --}}
          <div class="mb-3">
            <label class="form-label fw-bold">School Contact</label>
            <input type="text" name="school_contact" 
                   class="form-control @error('school_contact') is-invalid @enderror" 
                   value="{{ old('school_contact', $settings['school_contact'] ?? '') }}" required>
            @error('school_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i> Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
