<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Registrar Portal')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    body { background-color: #e6f3f9; margin: 0; }
    .sidebar { width: 220px; background: #fff; border-right: 1px solid #ddd;
               position: fixed; top: 0; bottom: 0; padding: 20px 15px; overflow-y:auto; }

    /* Sidebar items (links and dropdown button) */
    .sidebar a,
    .sidebar button.nav-link {
      display: block;
      width: 100%;
      text-align: left;
      padding: 8px 10px;
      color: #0077b6;
      font-weight: 500;
      text-decoration: none;
      border-radius: 5px;
      margin-bottom: 8px;
      cursor: pointer;
      background: transparent;
      border: none;
    }
    .sidebar a:hover,
    .sidebar a.active,
    .sidebar button.nav-link:hover {
      background: #f0f8ff;
    }

    /* Nested items inside Academic Management */
    .sidebar .collapse .nav-link {
      font-size: 0.9rem;
      margin-bottom: 4px;
    }

    .top-header { height: 60px; background: #fff; border-bottom: 1px solid #ddd;
                  padding: 0 20px; display: flex; align-items: center; justify-content: space-between; }
    .sub-header { background: #66c2e0; color: #fff; padding: 12px 20px; font-weight: 600; }
    .content { margin-left: 220px; padding: 0; }
    .card-custom { border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    #loader { display: none; position: fixed; top:50%; left:50%; transform:translate(-50%,-50%);
              font-size: 20px; color: #0077b6; }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="text-center mb-4">
      <img src="{{ auth()->user()->profile && auth()->user()->profile->profile_picture 
          ? asset('storage/'.auth()->user()->profile->profile_picture) 
          : asset('images/default-registrar.png') }}" 
          alt="Profile" class="rounded-circle mb-2" 
          style="width:80px;height:80px;object-fit:cover;">
      <h6 class="mb-0">
        {{ optional(auth()->user()->profile)->first_name }} 
        {{ optional(auth()->user()->profile)->last_name }}
      </h6>
    </div>

    <a href="{{ route('registrars.dashboard') }}" 
       class="nav-link {{ request()->routeIs('registrars.dashboard') ? 'active' : '' }}"
       data-ajax="true">
       <i class='bx bx-home me-2'></i> Dashboard
    </a>
    <a href="{{ route('registrars.docs.certs') }}" 
    class="nav-link 
    {{ 
      request()->routeIs('registrars.docs.certs') || 
      request()->routeIs('registrars.documents.all') || 
      request()->routeIs('registrars.certificates') 
      ? 'active' : '' 
    }}">
    <i class="bi bi-file-earmark-ruled-fill me-2"></i> Student Profile
    </a>
    <!----<a href="{{ route('registrars.students') }}" 
       class="nav-link {{ request()->routeIs('registrars.students*') ? 'active' : '' }}">
       <i class='bx bx-user me-2'></i> Student Records
    </a>---->
    <a href="{{ route('registrars.enrollment') }}" 
       class="nav-link {{ request()->routeIs('registrars.enrollment') ? 'active' : '' }}"
       data-ajax="true">
       <i class='bx bx-edit me-2'></i> Student Registration
    </a>
    <a href="{{ route('registrars.quarterSettings') }}" 
      class="nav-link {{ request()->routeIs('registrars.quarterSettings') ? 'active' : '' }}"
      data-ajax="true">
      <i class="bi bi-calendar-check me-2"></i> Quarter Control
    </a>
    <a href="{{ route('registrars.grades') }}" 
       class="nav-link {{ request()->routeIs('registrars.grades*') ? 'active' : '' }}"
       data-ajax="true">
       <i class='bx bx-book me-2'></i> Grades
    </a>

    {{-- Academic Management dropdown --}}
    @php
      $isAcademicActive = request()->routeIs('registrars.sections') || request()->routeIs('registrars.subjects*');
    @endphp
    <button class="nav-link d-flex align-items-center justify-content-between"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#academicMenu"
            aria-expanded="{{ $isAcademicActive ? 'true' : 'false' }}">
      <span><i class='bx bx-library me-2'></i> Academic Management</span>
      <i class="bi bi-chevron-down small"></i>
    </button>
    <div class="collapse {{ $isAcademicActive ? 'show' : '' }}" id="academicMenu">
      <a href="{{ route('registrars.sections') }}" 
         class="nav-link ms-3 {{ request()->routeIs('registrars.sections') ? 'active' : '' }}">
         <i class='bx bx-grid-alt me-2'></i> Sections
      </a>
      <a href="{{ route('registrars.subjects') }}" 
         class="nav-link ms-3 {{ request()->routeIs('registrars.subjects*') ? 'active' : '' }}"
         data-ajax="true">
         <i class='bx bx-book-open me-2'></i> Subjects
      </a>
    </div>

    <a href="{{ route('registrars.teachers') }}" 
       class="nav-link {{ request()->routeIs('registrars.teachers*') ? 'active' : '' }}">
       <i class='bx bx-chalkboard me-2'></i> Teacher Management
    </a>
    <a href="{{ route('registrars.curriculum') }}" 
       class="nav-link {{ request()->routeIs('registrars.curriculum*') ? 'active' : '' }}"
       data-ajax="true">
       <i class='bx bx-book-bookmark me-2'></i> Curriculum
    </a>
    <a href="{{ route('registrars.reports') }}" 
       class="nav-link {{ request()->routeIs('registrars.reports') ? 'active' : '' }}"
       data-ajax="true">
       <i class='bx bx-file me-2'></i> Reports
    </a>
    <a href="{{ route('registrars.settings') }}" 
       class="nav-link {{ request()->routeIs('registrars.settings') ? 'active' : '' }}"
       data-ajax="true">
       <i class='bx bx-cog me-2'></i> Settings
    </a>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="top-header">
      <div class="d-flex align-items-center">
        <img src="{{ asset('Mindware.png') }}" alt="School Logo" style="height:40px;" class="me-2">
        <span><strong>Children's Mindware School, Inc.</strong></span>
      </div>
      <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
          Hi, {{ optional(auth()->user()->profile)->first_name ?? 'Registrar' }} 👩‍💼
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="{{ route('registrars.settings') }}">
              <i class="bi bi-gear me-2"></i> Settings
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
          </li>
        </ul> 
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
      </div>
    </div>

    <div class="sub-header" id="page-header">@yield('header','Dashboard')</div>
    <div class="container my-4" id="main-content">
      @yield('content')
    </div>
  </div>

  <div id="loader"><i class='bx bx-loader bx-spin'></i> Loading...</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('.sidebar .nav-link[data-ajax="true"]').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.href;

        document.querySelector('#loader').style.display = 'block';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
          .then(res => res.text())
          .then(html => {
            let doc = new DOMParser().parseFromString(html, 'text/html');
            document.querySelector('#main-content').innerHTML = doc.querySelector('#main-content').innerHTML || '';
            document.querySelector('#page-header').innerHTML = doc.querySelector('#page-header').innerHTML || '';
            
            document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            window.history.pushState({}, '', url); 
            // Notify pages that content has changed so they can re-bind JS
            document.dispatchEvent(new Event('page:loaded'));
            document.querySelector('#loader').style.display = 'none';
          })
          .catch(err => {
            console.error(err);
            document.querySelector('#loader').style.display = 'none';
          });
      });
    });
  </script>
  @stack('scripts')
</body>
</html>
