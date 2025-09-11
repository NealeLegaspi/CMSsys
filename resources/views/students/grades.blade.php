@extends('layouts.student')

@section('title','My Grades')
@section('header','Grades')

@section('content')
<h1 class="text-2xl font-bold mb-4">My Grades</h1>

@if($grades->count())
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Subject</th>
        <th>Quarter</th>
        <th>Grade</th>
      </tr>
    </thead>
    <tbody>
      @foreach($grades as $g)
      <tr>
        <td>{{ $g->subject?->name ?? 'N/A' }}</td>
        <td>{{ $g->quarter ?? '-' }}</td>
        <td>{{ $g->grade ?? '-' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
@else
  <p>No grades available yet.</p>
@endif
@endsection
