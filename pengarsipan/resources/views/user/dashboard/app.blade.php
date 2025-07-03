<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.1.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter&amp;display=swap" rel="stylesheet"/>
    @stack('link')
    <title>@yield('title') - Sistem Pengarsipan</title>
</head>
<body class="bg-white text-black">
    <header class="flex items-center justify-between px-6 py-3">
        <div class="flex items-center space-x-3">
            <img alt="" class="w-12 h-12" src="{{ asset('images/logo.png') }}"/>
            <div class="mt-3">
                <p class="font-semibold text-sm">Sistem Informasi</p>
                <p class="font-semibold text-sm">Pengarsipan</p>
            </div>
        </div>
        <button id="logoutBtn" class="flex items-center space-x-2 bg-sky-300 hover:bg-sky-400 text-sky-700 rounded-full px-4 py-2 shadow-md text-sm font-medium">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
    </header>

    <main>
        <div class="mb-10 bg-[blue]">
            <div class="text-sm text-gray-600 py-4 pl-4 text-white">
                @yield('breadcurb')
            </div>
        </div>
        <div class="px-3">
            @yield('content')
        </div>
    </main>

    @stack('script')

    <script>
    $(document).ready(function(){
        // jika ada session success atau error, tampilkan sweetalert
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        // handle logout click
        $('#logoutBtn').click(function(){
            Swal.fire({
                title: 'Yakin ingin logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // kirim logout
                    $.post("{{ route('page.logout') }}", {_token: '{{ csrf_token() }}'}, function(){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil logout',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "{{ route('page.login') }}";
                        });
                    }).fail(function(){
                        Swal.fire({
                            icon: 'error',
                            title: 'Logout gagal',
                            text: 'Terjadi kesalahan saat logout.'
                        });
                    });
                }
            })
        });
    });
    </script>
</body>
</html>
