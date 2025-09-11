<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name', "Children's Mindware School, Inc.") }}</title>

  <!-- Bootstrap & Boxicons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #e6f3f9;
      color: #003366;
    }
    .hero {
      background: url('{{ asset("background.jpg") }}') no-repeat center center/cover;
      height: 615px;
      display: flex;
      align-items: flex-end;
      color: white;
    }
    .hero-content {
      background: rgba(0,0,0,0.5);
      padding: 20px;
      border-radius: 8px;
      max-width: 500px;
    }
    .section-title {
      font-weight: bold;
      font-size: 18px;
      background-color: #b3d9f2;
      padding: 8px 15px;
      display: inline-block;
      border-radius: 4px;
      margin: 20px 0;
    }
    footer {
      background-color: #6BBDE1;
      padding: 20px;
      text-align: center;
      font-size: 14px;
    }
    footer a {
      color: black;
      text-decoration: underline;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="{{ asset('Mindware.png') }}" alt="CMS Logo" height="40" class="me-2">
      <strong>Children's Mindware School, Inc.</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto fw-bold">
        <li class="nav-item mx-2"><a class="nav-link" href="#home">Home</a></li>
        <li class="nav-item mx-2"><a class="nav-link" href="#about">About Us</a></li>
        <li class="nav-item mx-2"><a class="nav-link" href="#contact">Contact</a></li>
        <li class="nav-item mx-2">
          <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero" id="home">
  <div class="container">
    <div class="hero-content">
      <h1>Unlock Your Child's Future with Quality Education</h1>
      <p>Join the Children's Mindware School family and experience a nurturing environment where young minds flourish. Our online enrollment system makes starting this journey simple and convenient.</p>
    </div>
  </div>
</section>

<!-- About -->
<section class="py-5" id="about" style="background-color:#cfe8f5;">
  <div class="container">
    <h2 class="mb-3">Welcome to Children's Mindware School!</h2>
    <p class="mb-4">
      At Children's Mindware School, Inc., we believe in nurturing young minds and fostering a lifelong love for learning.
      Our dedicated faculty and comprehensive curriculum are designed to provide a holistic educational experience that
      empowers students to reach their full potential.<br><br>
      We are committed to creating a safe, inclusive, and stimulating environment where every child feels valued and inspired
      to excel academically and personally. Discover how we can partner with you in shaping your child’s bright future.
    </p>

    <div class="section-title">Discover Student Life at CMS!</div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="card shadow-sm h-100 text-center p-3">
          <div class="fs-1 mb-2">📖</div>
          <h5 class="card-title text-primary">Foundational Learning</h5>
          <p class="card-text">Building strong roots in reading, writing, and math through engaging, hands-on activities that ignite curiosity and foster early academic success.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm h-100 text-center p-3">
          <div class="fs-1 mb-2">🏀</div>
          <h5 class="card-title text-primary">Active Minds, Healthy Bodies</h5>
          <p class="card-text">Move, play, and learn! Join our sports for all from basketball to fun games, helping every child find joy in being active and healthy.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm h-100 text-center p-3">
          <div class="fs-1 mb-2">🎯</div>
          <h5 class="card-title text-primary">Explore Your Interests</h5>
          <p class="card-text">Beyond books! Dive into exciting clubs like reading stories, playing games, and more, making new friends and discovering hidden talents.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer id="contact">
  <div class="container">
    <p class="fw-bold">Connect With Our Admissions Team</p>
    <p>We are here to help you every step of the way. Feel free to reach out to us for any inquiries.</p>
    <p>Sta. Francis Subd., Boni Ave., Batangas, Batangas</p>
    <p>09369210244</p>
    <p>Children's Mindware School Inc.</p>
    <p>© {{ date('Y') }} Children's Mindware School, Inc. All rights reserved.
      <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a>
    </p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
