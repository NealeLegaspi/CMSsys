<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title','Admin Portal')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    body { background-color: #e6f3f9; margin: 0; }
    .sidebar { width: 220px; background: #fff; border-right: 1px solid #ddd;
               position: fixed; top: 0; bottom: 0; padding: 20px 15px; overflow-y:auto; }
    .sidebar a { display: block; padding: 8px 10px; color: #0077b6; font-weight: 500;
                 text-decoration: none; border-radius: 5px; margin-bottom: 8px; cursor: pointer; }
    .sidebar a:hover, .sidebar a.active { background: #f0f8ff; }
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
          : asset('images/default-admin.png') }}" 
          alt="Profile" class="rounded-circle mb-2" 
          style="width:80px;height:80px;object-fit:cover;">
      <h6 class="mb-0">
        {{ optional(auth()->user()->profile)->first_name }} 
        {{ optional(auth()->user()->profile)->last_name }}
      </h6>
    </div>

    <!-- Navigation Links -->
    <a href="{{ route('admins.dashboard') }}" 
       class="nav-link {{ request()->routeIs('admins.dashboard') ? 'active' : '' }}">
       <i class="bx bx-home me-2"></i> Dashboard
    </a>
    <a href="{{ route('admins.announcements') }}" 
       class="nav-link {{ request()->routeIs('admins.announcements*') ? 'active' : '' }}">
       <i class="bi bi-megaphone me-2"></i> Announcements
    </a>
    <a href="{{ route('admins.student-records') }}" 
       class="nav-link {{ request()->routeIs('admins.student-records*') ? 'active' : '' }}">
       <i class='bx bx-id-card me-2'></i> Student Records
    </a>
    <a href="{{ route('admins.schoolyear') }}" 
       class="nav-link {{ request()->routeIs('admins.schoolyear') ? 'active' : '' }}">
       <i class='bx bx-calendar me-2'></i> School Year
    </a>
    <a href="{{ route('admins.subjects') }}" 
       class="nav-link {{ request()->routeIs('admins.subjects') ? 'active' : '' }}">
       <i class='bx bx-book me-2'></i> Subjects
    </a>
    <a href="{{ route('admins.users') }}" 
       class="nav-link {{ request()->routeIs('admins.users') ? 'active' : '' }}">
       <i class="bx bx-user me-2"></i> Users
    </a>
    <a href="{{ route('admins.reports') }}" 
       class="nav-link {{ request()->routeIs('admins.reports') ? 'active' : '' }}">
       <i class="bx bx-file me-2"></i> Reports
    </a>
    <a href="{{ route('admins.logs') }}" 
       class="nav-link {{ request()->routeIs('admins.logs') ? 'active' : '' }}">
       <i class="bx bx-list-ul me-2"></i> Activity Logs
    </a>
    <a href="{{ route('admins.settings') }}" 
       class="nav-link {{ request()->routeIs('admins.settings') ? 'active' : '' }}">
       <i class="bx bx-cog me-2"></i> Settings
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
          Hi, {{ optional(auth()->user()->profile)->first_name ?? 'Admin' }} 👨‍💼
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="{{ route('admins.settings') }}">⚙️ Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              🚪 Logout
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
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
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
            document.querySelector('#loader').style.display = 'none';
          })
          .catch(err => {
            console.error(err);
            document.querySelector('#loader').style.display = 'none';
          });
      });
    });
  </script>
</body>
</html>
