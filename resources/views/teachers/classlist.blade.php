@extends('layouts.teacher')

@section('title','Class List')
@section('header','Class List')

@section('content')
<div class="container my-4">
  <!-- Tabs -->
  <ul class="nav nav-tabs" id="classlistTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="advisory-tab" data-bs-toggle="tab" data-bs-target="#advisory" type="button" role="tab">
        Advisory Class
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">
        My Subjects
      </button>
    </li>
  </ul>

  <div class="tab-content mt-3">
    <!-- Advisory -->
    <div class="tab-pane fade show active" id="advisory" role="tabpanel">
      <h5>
        Class: {{ $sectionName ?? 'No Advisory Section Assigned' }}
      </h5>

      @if(!empty($sectionId))
        <p>
          Total Students: {{ count($studentsMale ?? []) + count($studentsFemale ?? []) }}
        </p>

        <!-- Male Students -->
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            Male ({{ count($studentsMale ?? []) }})
          </div>
          <div class="card-body">
            @if(!empty($studentsMale) && count($studentsMale) > 0)
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>LRN</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($studentsMale as $st)
                  <tr>
                    <td>{{ $st->lrn ?? 'N/A' }}</td>
                    <td>{{ $st->last_name ?? '' }}</td>
                    <td>{{ $st->first_name ?? '' }}</td>
                    <td>{{ $st->middle_name ?? '' }}</td>
                    <td>
                      @if(($st->status ?? '') === 'active')
                        <span class="text-success fw-bold">Active</span>
                      @else
                        <span class="text-danger fw-bold">Inactive</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <p>No male students enrolled.</p>
            @endif
          </div>
        </div>

        <!-- Female Students -->
        <div class="card mb-4">
          <div class="card-header text-white" style="background-color:#ff69b4">
            Female ({{ count($studentsFemale ?? []) }})
          </div>
          <div class="card-body">
            @if(!empty($studentsFemale) && count($studentsFemale) > 0)
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>LRN</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($studentsFemale as $st)
                  <tr>
                    <td>{{ $st->lrn ?? 'N/A' }}</td>
                    <td>{{ $st->last_name ?? '' }}</td>
                    <td>{{ $st->first_name ?? '' }}</td>
                    <td>{{ $st->middle_name ?? '' }}</td>
                    <td>
                      @if(($st->status ?? '') === 'active')
                        <span class="text-success fw-bold">Active</span>
                      @else
                        <span class="text-danger fw-bold">Inactive</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <p>No female students enrolled.</p>
            @endif
          </div>
        </div>
      @else
        <p>No advisory section assigned to you yet.</p>
      @endif
    </div>

    <!-- Subjects -->
    <div class="tab-pane fade" id="subjects" role="tabpanel">
      <h5>Subjects You Handle</h5>
      @if(!empty($mySubjects) && count($mySubjects) > 0)
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Grade Level</th>
              <th>Section</th>
              <th>Subject</th>
            </tr>
          </thead>
          <tbody>
            @foreach($mySubjects as $sub)
            <tr>
              <td>{{ $sub->gradelevel ?? 'N/A' }}</td>
              <td>{{ $sub->section_name ?? 'N/A' }}</td>
              <td>{{ $sub->subject_name ?? 'N/A' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p>No subjects assigned to you yet.</p>
      @endif
    </div>
  </div>
</div>
@endsection
