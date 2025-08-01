@extends('user.dashboard.app')

@section('title', "Agunan")

@section('breadcurb')
    <a href="{{ route('user.page.dashboard') }}" class="p-3 text-blue-600 hover:underline text-white">
        Dashboard <i class="fa fa-dashboard"></i>
    </a> &gt;
    <a href="{{ route('user.page.agunan') }}" class="p-3 text-blue-600 hover:underline text-white">
        Agunan</i>
    </a> &gt;
@endsection

@section('content')
    {{-- Tombol tambah Agunan --}}
    <div class="pb-4 flex flex-row items-center">
        <a href="#" data-bs-toggle="modal" data-bs-target="#tambahAgunan" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Agunan
        </a>
    </div>

    {{-- Modal tambah Agunan --}}
    <div class="modal fade" id="tambahAgunan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title font-bold">Tambah Agunan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadAgunanForm" action="{{ route('user.page.agunan.tambah') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun Agunan</label>
                            <input type="number" name="tahun" class="form-control" required placeholder="Contoh: 2025">
                        </div>
                        <div class="mb-3">
                            <label for="agunans" class="form-label">Pilih File Agunan</label>
                            <input type="file" name="agunans[]" class="form-control" id="agunansInput" multiple required>
                            <small class="text-muted">Bisa pilih lebih dari satu file sekaligus</small>
                        </div>

                        <div class="mb-3">
                            <div class="progress" style="height: 20px; display: none;" id="uploadAgunanProgress">
                                <div class="progress-bar bg-success" id="agunanProgressBar" style="width: 0%">0%</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-success" id="submitAgunanBtn">
                                Simpan <i class="fas fa-save fa-lg"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tampilkan list tahun agunan --}}
    <div class="grid sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">
        @forelse ($dataAgunan as $agunan)
            <a href="{{ url('/User/Agunan/'.$agunan->tahun) }}" class="rounded-lg">
                <div class="bg-[#0012cf] rounded-lg p-4 hover:bg-blue-500 transition duration-300 ease-in-out">
                    <div class="flex flex-col items-center">
                        <i class="fa fa-archive fa-2x text-white mb-2"></i>
                        <b class="text-white font-bold">{{ $agunan->tahun }}</b>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center text-gray-400">Belum ada data agunan.</div>
        @endforelse
    </div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.getElementById('submitAgunanBtn').addEventListener('click', function (e) {
    e.preventDefault(); 

    const tahun = document.querySelector('input[name="tahun"]').value.trim();
    const files = document.querySelector('input[name="agunans[]"]').files;

    // 🔍 Validasi kosong
    if (!tahun || files.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Tahun agunan dan file wajib diisi.',
        });
        return;
    }

    const form = document.getElementById('uploadAgunanForm');
    const formData = new FormData(form);

    const progressContainer = document.getElementById('uploadAgunanProgress');
    const progressBar = document.getElementById('agunanProgressBar');

    // Reset progress bar
    progressBar.style.width = '0%';
    progressBar.innerHTML = '0%';
    progressContainer.style.display = 'block';

    // Kirim AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "{{ route('user.page.agunan.tambah') }}", true);
    xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");

    xhr.upload.addEventListener("progress", function (e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressBar.innerHTML = percent + '%';
        }
    });

    xhr.onload = function () {
        if (xhr.status === 200) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Agunan berhasil diunggah',
                timer: 4000,
                showConfirmButton: false
            }).then(() => location.reload());
        } else if (xhr.status === 422) {
            // Tangkap validasi gagal dari Laravel
            const res = JSON.parse(xhr.responseText);
            const errorMessages = Object.values(res.errors).flat().join('<br>');
            Swal.fire({
                icon: 'error',
                title: 'Gagal Validasi!',
                html: errorMessages
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat mengunggah agunan',
            });
        }
    };

    xhr.onerror = function () {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Upload gagal karena koneksi atau kesalahan server',
        });
    };

    xhr.send(formData);
  });
</script>
@endpush
