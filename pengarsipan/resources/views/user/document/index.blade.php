@extends('user.dashboard.app')

@section('title',"Dokumen")

@section('breadcurb')
    <a href="{{route('user.page.dashboard')}}" class="p-3 text-blue-600 hover:underline text-white">
        Dashboard <i class="fa fa-dashboard"></i>
    </a> &gt;
    <a href="{{route('user.page.document')}}" class="p-3 text-blue-600 hover:underline text-white">
        Document <i class="fa fa-dashboard"></i>
    </a> &gt;
@endsection

@section('content')
    {{-- Tombol tambah Document --}}
    <div class="pb-4 flex flex-row items-center">
        <a href="#" data-bs-toggle="modal" data-bs-target="#tambahDocument" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Document
        </a>
    </div>

    {{-- Modal tambah Document --}}
   <div class="modal fade" id="tambahDocument" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title font-bold">Tambah Berkas</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadDocumentForm" onsubmit="return false;" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun Berkas</label>
                            <input type="number" name="tahun" class="form-control" required placeholder="Contoh: 2025">
                        </div>
                        <div class="mb-3">
                            <label for="berkas" class="form-label">Pilih File Berkas</label>
                            <input type="file" name="documents[]" class="form-control" id="berkasInput" multiple required>
                            <small class="text-muted">Bisa pilih lebih dari satu file sekaligus</small>
                        </div>

                        <div class="mb-3">
                            <div class="progress" style="height: 20px; display: none;" id="uploadDocumentProgress">
                                <div class="progress-bar bg-success" id="documentProgressBar" style="width: 0%">0%</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-success">
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
        @forelse ($dataDocument ?? [] as $document)
            <a href="{{ url('/User/Document/'.$document->tahun) }}" class="rounded-lg">
                <div class="bg-[#0012cf] rounded-lg p-4 hover:bg-blue-500 transition duration-300 ease-in-out">
                    <div class="flex flex-col items-center">
                        <i class="fa fa-book fa-2x text-white mb-2"></i>
                        <b class="text-white font-bold">{{ $document->tahun }}</b>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center text-gray-400">Belum ada dokumen.</div>
        @endforelse
    </div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const MAX_SIZE_MB = 1024;
    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;
    const documentsInput = document.getElementById('berkasInput');
    const form = document.getElementById('uploadDocumentForm');
    const progressBar = document.getElementById('documentProgressBar');
    const progressContainer = document.getElementById('uploadDocumentProgress');

    // Validasi ukuran file
    documentsInput.addEventListener('change', function () {
        let oversizedFiles = [];

        for (const file of documentsInput.files) {
            if (file.size > MAX_SIZE_BYTES) {
                oversizedFiles.push(`"${file.name}" (${(file.size / (1024 * 1024)).toFixed(2)} MB)`);
            }
        }

        if (oversizedFiles.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                html: `File berikut melebihi batas 50MB:<br><ul style="text-align: left;">` +
                      oversizedFiles.map(f => `<li>${f}</li>`).join('') +
                      `</ul>`,
                confirmButtonText: 'OK'
            });

            documentsInput.value = ''; // Reset input file
        }
    });

    // Submit upload dokumen via AJAX + progress
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const tahun = form.querySelector('input[name="tahun"]').value;
        const files = documentsInput.files;

        if (!tahun || files.length === 0) {
            Swal.fire('Lengkapi Data', 'Tahun dan File wajib diisi.', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('tahun', tahun);
        for (let i = 0; i < files.length; i++) {
            formData.append('documents[]', files[i]);
        }

        // CSRF token
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('_token', csrfToken);

        // Tampilkan progress bar
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';
        progressBar.innerText = '0%';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', "{{ route('user.page.document.tambah') }}", true);

        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressBar.innerText = percent + '%';
            }
        });

        xhr.onload = function () {
            if (xhr.status === 200) {
                Swal.fire('Berhasil', 'Dokumen berhasil diunggah.', 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Gagal', 'Terjadi kesalahan saat upload.', 'error');
                progressContainer.style.display = 'none';
            }
        };

        xhr.onerror = function () {
            Swal.fire('Gagal', 'Terjadi kesalahan jaringan.', 'error');
            progressContainer.style.display = 'none';
        };

        xhr.send(formData);
    });

    // SweetAlert dari session Laravel (jika ada)
    @if(session('message') && session('type'))
        Swal.fire({
            icon: '{{ session("type") }}',
            title: '{{ session("type") == "success" ? "Berhasil" : "Gagal" }}',
            text: '{{ session("message") }}',
            timer: 2500,
            showConfirmButton: false
        });
    @endif
</script>
@endpush

