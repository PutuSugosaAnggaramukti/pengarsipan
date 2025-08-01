@extends('user.dashboard.app')

@section('title', "Dokumen")

@section('breadcurb')
    <a href="{{ route('user.page.dashboard') }}" class="p-3 text-blue-600 hover:underline text-white">
        Dashboard <i class="fa fa-dashboard"></i>
    </a> &gt;
    <a href="{{ route('user.page.document') }}" class="p-3 text-blue-600 hover:underline text-white">
        Dokumen
    </a> &gt;
@endsection

@section('content')
    {{-- Tombol tambah dokumen --}}
    <div class="pb-4 flex flex-row items-center">
        <a href="#" data-bs-toggle="modal" data-bs-target="#tambahDokumen" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Dokumen
        </a>
    </div>

    {{-- Modal tambah dokumen --}}
    <div class="modal fade" id="tambahDokumen" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title font-bold">Tambah Dokumen</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadDocumentForm" action="{{ route('user.page.document.tambah') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun Dokumen</label>
                            <input type="number" name="tahun" class="form-control" required placeholder="Contoh: 2025">
                        </div>
                        <div class="mb-3">
                            <label for="dokumens" class="form-label">Pilih File Dokumen</label>
                            <input type="file" name="documents[]" class="form-control" id="dokumensInput" multiple required>
                            <small class="text-muted">Bisa pilih lebih dari satu file sekaligus</small>
                        </div>

                        <div class="mb-3">
                            <div class="progress" style="height: 20px; display: none;" id="uploadDokumenProgress">
                                <div class="progress-bar bg-success" id="dokumenProgressBar" style="width: 0%">0%</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-success" id="submitDokumenBtn">
                                Simpan <i class="fas fa-save fa-lg"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tampilkan list tahun dokumen --}}
    <div class="grid sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">
        @forelse ($dataDocument as $document)
            <a href="{{ url('/User/Document/'.$document->tahun) }}" class="rounded-lg">
                <div class="bg-[#0012cf] rounded-lg p-4 hover:bg-blue-500 transition duration-300 ease-in-out">
                    <div class="flex flex-col items-center">
                        <i class="fa fa-archive fa-2x text-white mb-2"></i>
                        <b class="text-white font-bold">{{ $document->tahun }}</b>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center text-gray-400">Belum ada data dokumen.</div>
        @endforelse
    </div>
@endsection

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session("error") }}',
    });
</script>
@endif


@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('uploadDocumentForm').addEventListener('submit', function(e) {
    const tahun = document.querySelector('input[name="tahun"]').value.trim();
    const files = document.querySelector('input[name="documents[]"]').files;

    if (!tahun || files.length === 0) {
        e.preventDefault(); // Stop form
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Tahun dokumen dan file wajib diisi!',
        });
    }
});
</script>


<script>
 document.getElementById('submitDokumenBtn').addEventListener('click', function (e) {
    e.preventDefault();

    const tahun = document.querySelector('input[name="tahun"]').value.trim();
    const files = document.querySelector('input[name="documents[]"]').files;

    if (!tahun || files.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Tahun dokumen dan file wajib diisi!',
        });
        return;
    }

    const form = document.getElementById('uploadDocumentForm');
    const formData = new FormData(form);

    const progressContainer = document.getElementById('uploadDokumenProgress');
    const progressBar = document.getElementById('dokumenProgressBar');

    // Reset progress bar
    progressBar.style.width = '0%';
    progressBar.innerHTML = '0%';
    progressContainer.style.display = 'block';

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "{{ route('user.page.document.tambah') }}", true);
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
                text: 'Dokumen berhasil diunggah',
                timer: 3000,
                showConfirmButton: false
            }).then(() => location.reload());
        } else if (xhr.status === 422) {
            const response = JSON.parse(xhr.responseText);
            const errors = Object.values(response.errors).flat().join('\n');
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: errors
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat mengunggah dokumen',
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
