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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- ✅ SweetAlert2 --}}
    @stack('link')
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>@yield('title') - Sistem Pengarsipan</title>
</head>
<body class="bg-white text-black">
    <header class="flex items-center justify-between px-6">

        <div class="flex items-center space-x-3">
            <img alt="" class="w-12 h-12" height="48" src="{{ asset('images/logo.png') }}" width="48"/>
            <div class="py-3">
                <p class="font-semibold text-sm">Sistem Informasi</p>
                <p class="font-semibold text-sm">Pengarsipan</p>
            </div>
        </div>

        <button id="logoutBtn" class="flex items-center space-x-2 bg-sky-300 hover:bg-sky-400 text-sky-700 rounded-full px-4 py-2 shadow-md text-sm font-medium" type="button">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>


        {{-- modal logout --}}
        <div class="modal" id="logoutModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal body -->
                    <div class="modal-body">
                        <h1 class="font-bold text-black text-xl mb-10">Yakin ingin keluar?</h1>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <a href="{{ route('page.logout.sy') }}" class="btn btn-secondary">Ya</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end modal logout --}}

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

    {{-- SweetAlert Notifikasi Global --}}
   @if(session('message') && session('type'))
<script>
    const alertTitles = {
        success: 'Berhasil',
        error: 'Gagal',
        warning: 'Peringatan',
        info: 'Informasi'
    };

    Swal.fire({
        icon: "{{ session('type') }}",
        title: alertTitles["{{ session('type') }}"] || 'Informasi',
        text: "{{ session('message') }}",
        timer: ["success", "info"].includes("{{ session('type') }}") ? 2500 : undefined,
        showConfirmButton: !["success", "info"].includes("{{ session('type') }}")
    });
</script>
@endif
    @stack('script')
    <script>
    document.getElementById('logoutBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Yakin ingin keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('page.logout.sy') }}";
            }
        });
    });
</script>

</body>
</html>
