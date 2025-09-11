@extends('layouts.teacher')

@section('title','Grades')
@section('header','Grades')

@section('content')
<div class="container my-4">
  <!-- Tabs -->
  <ul class="nav nav-tabs" id="gradesTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="advisory-tab" data-bs-toggle="tab" data-bs-target="#advisory" type="button" role="tab">Advisory Class</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">My Subjects</button>
    </li>
  </ul>

  <div class="tab-content mt-3">
    <!-- Advisory Class Tab -->
    <div class="tab-pane fade show active" id="advisory" role="tabpanel">
      @if(!empty($advisorySection))
        <h5 class="mt-3">My Advisory</h5>
        <p>
          <strong>Grade Level:</strong> {{ $advisorySection['gradelevel_name'] }} <br>
          <strong>Section:</strong> {{ $advisorySection['section_name'] }}
        </p>

        <form method="POST" action="{{ route('teacher.grades.save') }}">
          @csrf
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>LRN</th>
                <th>Student Name</th>
                <th>1st Quarter</th>
                <th>2nd Quarter</th>
                <th>3rd Quarter</th>
                <th>4th Quarter</th>
              </tr>
            </thead>
            <tbody>
              @foreach($students as $id => $s)
              <tr>
                <td>{{ $s['lrn'] }}</td>
                <td>{{ $s['name'] }}</td>
                @foreach(['1st','2nd','3rd','4th'] as $q)
                  <td>
                    <input type="number" name="grades[{{ $id }}][{{ $q }}]"
                           value="{{ $s['grades'][$q] ?? '' }}"
                           min="60" max="100">
                  </td>
                @endforeach
              </tr>
              @endforeach
            </tbody>
          </table>
          <button type="submit" class="btn btn-primary">Save Grades</button>
        </form>
      @else
        <p class="mt-3">You are not assigned as an adviser of any section.</p>
      @endif
    </div>

    <!-- My Subjects Tab -->
    <div class="tab-pane fade" id="subjects" role="tabpanel">
      <h5 class="mt-3">My Subjects</h5>
      @if(!empty($mySubjects) && count($mySubjects) > 0)
        <ul class="list-group">
          @foreach($mySubjects as $sub)
            <li class="list-group-item">
              {{ $sub['subject_name'] }} - {{ $sub['gradelevel_name'] }} - {{ $sub['section_name'] }}
              <a href="{{ route('teacher.grades.encode', ['subject_id'=>$sub['subject_id'],'section_id'=>$sub['section_id']]) }}"
                 class="btn btn-sm btn-primary float-end">Enter Grades</a>
            </li>
          @endforeach
        </ul>
      @else
        <p>You have no assigned subjects.</p>
      @endif
    </div>
  </div>
</div>
@endsection