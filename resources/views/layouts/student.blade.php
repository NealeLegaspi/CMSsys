<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Student Portal')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    body { background-color: #e6f3f9; margin: 0; }
    .sidebar { width: 220px; background: #fff; border-right: 1px solid #ddd;
               position: fixed; top: 0; bottom: 0; padding: 20px 15px; }
    .sidebar a { display: block; padding: 8px 10px; color: #0077b6; font-weight: 500;
                 text-decoration: none; border-radius: 5px; margin-bottom: 8px; cursor: pointer; }
    .sidebar a:hover, .sidebar a.active { background: #f0f8ff; }
    .top-header { height: 60px; background: #fff; border-bottom: 1px solid #ddd; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; }
    .sub-header { background: #66c2e0; color: #fff; padding: 12px 20px; font-weight: 600; }
    .content { margin-left: 220px; padding: 0; }
    .card-custom { border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="text-center mb-4">
      <img src="{{ asset(auth()->user()->profile->profile_picture ?? 'images/default.png') }}" 
           alt="Profile" class="rounded-circle mb-2" style="width:80px;height:80px;object-fit:cover;">
      <h5 class="mb-0">{{ auth()->user()->profile->first_name ?? auth()->user()->email }}</h5>
    </div>
    <a href="{{ route('students.dashboard') }}" class="nav-link {{ request()->routeIs('students.dashboard') ? 'active' : '' }}">
        <i class='bx bx-home me-2'></i>Dashboard
    </a>
    <a href="{{ route('students.announcements') }}" class="nav-link {{ request()->routeIs('students.announcements') ? 'active' : '' }}">
        <i class='bx bx-bell me-2'></i>Announcements
    </a>
    <a href="{{ route('students.grades') }}" class="nav-link {{ request()->routeIs('students.grades') ? 'active' : '' }}">
        <i class='bx bx-book me-2'></i>Grades
    </a>
    <a href="{{ route('students.settings') }}" class="nav-link {{ request()->routeIs('students.settings') ? 'active' : '' }}">
        <i class='bx bx-cog me-2'></i>Settings
    </a>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="top-header">
      <div class="d-flex align-items-center">
        <img src="{{ asset('Mindware.png') }}" alt="School Logo" style="height:40px;" class="me-2">
        <span><strong>Children's Mindware School, Inc.</strong></span>
      </div>
      <div>
        <span class="me-2">Hi, {{ auth()->user()->profile->first_name ?? 'Student' }}! 🧑‍🎓</span>
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="btn btn-sm btn-outline-danger">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
      </div>
    </div>

    <div class="sub-header" id="page-header">@yield('header','Dashboard')</div>

    <div class="container my-4" id="main-content">
      @yield('content')
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');

        fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
          .then(res => {
            if(!res.ok) throw new Error('Failed to load page');
            return res.text();
          })
          .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');
            document.querySelector('#main-content').innerHTML =
                doc.querySelector('#main-content').innerHTML || '';
            document.querySelector('#page-header').innerHTML =
                doc.querySelector('#page-header').innerHTML || '';
          })
          .catch(err => console.error(err));
      });
    });

    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });
  </script>
</body>
</html>
