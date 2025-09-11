<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', "Children's Mindware School Inc") }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            background: #78c8e2;
        }
        .fade-form {
            opacity: 0;
            visibility: hidden;
            height: 0;
            overflow: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, height 0.3s ease;
        }
        .fade-form.show {
            opacity: 1;
            visibility: visible;
            height: auto;
            overflow: visible;
        }
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex justify-content-center align-items-center py-5" style="min-height: 100vh;">
    <div class="container" style="max-width: 500px;">
        <!-- Logo + Title -->
        <div class="text-center mb-4">
            <a href="{{ url('/') }}">
                <img src="{{ asset('Mindware.png') }}" alt="Mindware" width="100" height="100" class="rounded-circle mx-auto d-block">
            </a>
            <h2 class="text-white mt-3 fw-bold" style="font-size: 1.8rem;">
                Children's Mindware School Inc
            </h2>
        </div>

        <!-- Auth Forms -->
        <div class="bg-white rounded p-4 shadow fade-in">
            {{ $slot }}
        </div>
    </div>

    <script>
        function showForm(formId) {
            let activeForm = document.querySelector('.fade-form.show');
            if (activeForm) {
                activeForm.classList.remove('show');
                setTimeout(() => {
                    document.getElementById(formId).classList.add('show');
                }, 120); 
            } else {
                document.getElementById(formId).classList.add('show');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
