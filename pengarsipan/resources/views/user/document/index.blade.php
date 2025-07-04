@extends('user.dashboard.app')

@section('title', "Document " . $dataBerkas['th'])

@push('link')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.tailwindcss.css">
@endpush

@section('breadcurb')
    <a href="/user/dashboard" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]">Dashboard</a> &gt;
    <a href="#" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]">
        Document {{ $dataBerkas["th"] }}
    </a>
@endsection

@section('content')
<div class="my-3">
    <a class="px-3 py-3 rounded-lg text-white bg-[#198754] hover:bg-[#00ec7d] no-underline font-bold cursor-pointer"
       data-bs-toggle="modal" data-bs-target="#tambahBerkas">
        <i class="fa fa-plus fa-lg"></i>
        Tambah Berkas
    </a>
</div>

<div class="mt-5">
    <table id="example" class="table table-hover min-w-full divide-y text-sm mt-3">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tahun</th>
                <th>Nama Berkas</th>
                <th>Option</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection
{{-- Modal Detail Berkas --}}
<div class="modal fade" id="detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Berkas PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body space-y-2">
                <p><strong>User Upload:</strong> <span id="detailUser"></span></p>
                <p><strong>Waktu Upload:</strong> <span id="detailTime"></span></p>
            </div>
        </div>
    </div>
</div>
{{-- End modal detail berkas --}}
{{-- Modal Upload --}}
<div class="modal fade" id="tambahBerkas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadForm" action="{{ route('user.document.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="note text-sm mb-2">Maksimal size file per PDF 200MB</div>
                    <label>Year:</label>
                  <input type="text" name="tahun" value="{{ $dataBerkas['th'] }}" readonly class="w-full p-2 mb-3 rounded bg-gray-100">

                    <label>Choose PDF files:</label>
                  <input type="file" name="file[]" multiple accept="application/pdf" class="w-full p-2 mb-3 border rounded">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- modal view berkas --}}
<div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Preview PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfIframe" src="" width="100%" height="600px" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
{{-- end modal view berkas --}}

{{-- Modal Delete --}}
<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Berkas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah anda yakin ingin menghapus berkas ini?</p>
                    <input type="hidden" name="id" id="delete_id">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- end modal delete berkas --}}

@push('script')
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.tailwindcss.js"></script>
<script>
$(document).ready(function(){

    // CSRF untuk semua AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            withCredentials: true
        }
    });

    // Inisialisasi DataTables
var table = $('#example').DataTable({
    processing: true,
    serverSide: false,
    order: [[1, 'desc']], // order by tanggal desc
    ajax: {
        url: "{{ route('user.page.document.post.berkas') }}",
        type: "POST",
        data: {
            data: "{{ $dataBerkas['th'] }}",
            _token: "{{ csrf_token() }}"
        },
        dataSrc: function(json) {
            console.log("=== DATA RECEIVED FROM SERVER ===", json);
            window._myJsonData = json;
            return json.data || [];
        },
        error: function(xhr, status, error) {
            console.error("=== DATATABLE AJAX ERROR ===", status, error, xhr.responseText);
        },
        complete: function() {
            console.log("=== DATATABLE AJAX COMPLETE ===");
        }
    },
    columns: [
        { data: null, render: function(data, type, row, meta) {
            return meta.row + 1;
        }},
        { data: 'created_at', render: function(data, type, row) {
            if (!data) return '-';
            let date = new Date(data);
            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }},
        { data: 'tahun' },
        { data: 'nama_berkas' },
        { data: null, render: function(data, type, row) {
            return `<div class='flex'>
                <a href="#" onclick="previewPDF(${row.id})" class="text-center px-4 text-[blue] no-underline cursor-pointer hover:bg-[#eaea] rounded-lg py-2">
                    <i class="fa fa-eye fa-lg block"></i><b class="block mt-1">Preview</b>
                </a>
                <a href="#" onclick="openDeleteModal(${row.id})" class="text-center px-4 text-[red] no-underline cursor-pointer hover:bg-[#eaea] rounded-lg py-2">
                    <i class="fa fa-trash fa-lg block"></i><b class="block mt-1">Delete</b>
                </a>
                <a onclick="detailBerkas(${row.id})"
                class="text-center px-4 text-[blue] cursor-pointer no-underline hover:bg-[#eaea] rounded-lg py-2">
                    <i class="fa fa-info-circle fa-lg block"></i>
                    <b class="block mt-1">Info</b>
                </a>
            </div>`;
        }}
    ]
});


    // Upload form
$('#uploadForm').on('submit', function(e){
    e.preventDefault();

    // Cek apakah input file kosong
    if ($('input[name="file[]"]')[0].files.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak ada file',
            text: 'Silakan pilih file PDF sebelum mengupload.'
        });
        return; // stop submit
    }

    var formData = new FormData(this);
    formData.append('device_time', getLocalDateTime());

    Swal.fire({
        title: 'Mengupload...',
        html: 'Harap tunggu file sedang diupload.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Upload Berhasil',
                text: response.message,
                timer: 2000,
                showConfirmButton: false
            });
            $('#uploadForm')[0].reset(); // <-- reset form (bersihkan input file)
            $('#tambahBerkas').modal('hide');
            table.ajax.reload();
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Upload Gagal',
                text: 'Periksa ukuran atau format file.'
            });
        }
    });
});


function getLocalDateTime() {
    let now = new Date();
    return now.getFullYear()
         + "-" + String(now.getMonth() + 1).padStart(2, '0')
         + "-" + String(now.getDate()).padStart(2, '0')
         + " " + String(now.getHours()).padStart(2, '0')
         + ":" + String(now.getMinutes()).padStart(2, '0')
         + ":" + String(now.getSeconds()).padStart(2, '0');
}


    // Delete
 $('#deleteForm').on('submit', function(e){
    e.preventDefault();
    var id = $('#delete_id').val();

    Swal.fire({
        title: 'Menghapus...',
        html: 'Harap tunggu berkas sedang dihapus.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
        url: "/user/document/delete/" + id,
        method: "DELETE",
        data: {
            _token: "{{ csrf_token() }}"
        },
        xhrFields: { withCredentials: true },
        success: function(response){
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Berkas berhasil dihapus.',
                timer: 2000,
                showConfirmButton: false
            });
            $('#modalDelete').modal('hide');
            table.ajax.reload();
        },
        error: function(xhr){
            console.log(xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal menghapus berkas.'
            });
        }
    });
});


    // DataTables style tweaks
    $('#dt-length-0').addClass('bg-white mr-4');
    $('#dt-search-0').addClass('bg-white text-gray-800 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring focus:ring-blue-300');
    $('#example thead tr').addClass('text-black');

});

// fungsi modal detail
function detailBerkas(id) {
    Swal.showLoading();
    $.ajax({
        url: "/user/document/detail/" + id,
        method: "GET",
        success: function(response) {
            console.log("=== DETAIL RESPONSE ===", response);

            let waktuUpload = response.waktu_upload ?? '-';
            if (waktuUpload.includes(" ")) {
                let tanggal = waktuUpload.split(" ")[0];
                let jamSekarang = new Date();
                let timeNow = String(jamSekarang.getHours()).padStart(2, '0') + ":" +
                              String(jamSekarang.getMinutes()).padStart(2, '0') + ":" +
                              String(jamSekarang.getSeconds()).padStart(2, '0');
                waktuUpload = tanggal + " " + timeNow;
            }

            $('#detailUser').text(response.user ?? '-');
            $('#detailTime').text(waktuUpload);
            Swal.close();
            $('#detail').modal('show');
        },
        error: function(xhr) {
            console.log("=== DETAIL ERROR ===", xhr.status, xhr.responseText);
            Swal.fire('Error', 'Gagal memuat detail berkas.', 'error');
        }
    });
}
// fungsi modal delete
function openDeleteModal(id){
    $('#delete_id').val(id);
    $('#modalDelete').modal('show');
}

function previewPDF(id) {
    console.log("Memuat preview PDF untuk ID:", id);

    $.ajax({
        url: "{{ url('/user/document/show') }}/" + id,
        method: "GET",
        xhrFields: { withCredentials: true },
        success: function(response) {
            console.log("Data preview diterima:", response);

            if (response && response.file) {
                $('#previewTitle').text("Preview: " + response.nama);
                $('#pdfIframe').attr('src', response.file);
                $('#modalPreview').modal('show');
            } else {
                console.error("Response JSON tidak sesuai:", response);
                alert("Data preview tidak lengkap.");
            }
        },
        error: function(xhr) {
            console.error("AJAX Gagal:", xhr.status, xhr.statusText);
            console.error("Response:", xhr.responseText);
            alert('Gagal memuat PDF.');
        }
    });
}
</script>
@endpush

