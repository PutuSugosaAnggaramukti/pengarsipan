<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>Login - Sistem Informasi Pengarsipan</title>

    {{-- CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        img.logo {
            width: 90px;
            height: 90px;
            margin: 0 auto 1rem auto;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 relative">

    {{-- Notifikasi SweetAlert --}}
    @if(session('message') && session('type'))
    <script>
        Swal.fire({
            icon: "{{ session('type') }}",
            title: "{{ session('type') == 'success' ? 'Berhasil' : 'Gagal' }}",
            text: "{{ session('message') }}",
            timer: {{ session('type') == 'success' ? 2500 : 'null' }},
            showConfirmButton: {{ session('type') == 'success' ? 'false' : 'true' }}
        });
    </script>
    @endif

    {{-- Login Box --}}
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl text-center">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">

        <h1 class="text-2xl font-bold text-gray-800 mb-1">Sistem Informasi Pengarsipan</h1>
        <p class="text-sm text-gray-600 mb-6">Silahkan Login Untuk Melanjutkan</p>

        <form action="{{ route('page.login.post') }}" method="POST" class="text-left">
            @csrf

            {{-- Username --}}
            <div class="mb-4">
                <label class="block mb-1 text-gray-700 text-sm">Username</label>
                <input required type="text" name="username"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Username">
            </div>

            {{-- Password --}}
            <div class="mb-6 relative">
                <label class="block mb-1 text-gray-700 text-sm">Password</label>
                <input required type="password" id="password" name="password"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Password">
                <div class="absolute right-3 bottom-2.5 cursor-pointer text-gray-500 hover:text-gray-800" id="show-hide">
                    <i class="fa fa-eye"></i>
                </div>
            </div>

            {{-- Tombol Login --}}
            <button type="submit"
                    class="w-full bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 rounded-md shadow-md flex items-center justify-center gap-2 transition">
                <i class="fa fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>

    {{-- Pure JS Show/Hide Password --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggle = document.getElementById("show-hide");
            const input = document.getElementById("password");
            const icon = toggle.querySelector("i");

            toggle.addEventListener("click", function () {
                const type = input.getAttribute("type");
                if (type === "password") {
                    input.setAttribute("type", "text");
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    input.setAttribute("type", "password");
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            });
        });
    </script>

</body>
</html>
