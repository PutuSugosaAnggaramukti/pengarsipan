@extends('user.dashboard.app')

@section('title',"Dokumen " . $dataDocument["th"])

@push('link')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.tailwindcss.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('breadcurb')
    <a href="{{ route('user.page.dashboard') }}" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]">Dashboard</a> &gt;
    <a href="{{ route('user.page.document') }}" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]">Dokumen</a> &gt;
    <a onclick="dataDokumen({{ $dataDocument['th'] }})" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]" data-bs-toggle="modal" data-bs-target="#menuDokumen">
        {{ $dataDocument["th"] }}
    </a>

    <div class="modal" id="menuDokumen">
        <div class="modal-dialog w-full">
            <div class="modal-content">
                <div class="modal-header">
                    <b class="modal-title">Dokumen</b>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="mt-5">
    <button id="hapusTerpilih" class="btn btn-danger mb-3">
        <i class="fa fa-trash"></i> Hapus Terpilih
    </button>
    <div class="overflow-x-auto">
        <table id="example" class="table table-hover table-striped min-w-full divide-y text-sm mt-3">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Dokumen</th>
                    <th class="w-[220px] whitespace-nowrap">Option</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

<!-- Modal Detail -->
<div class="modal" id="detailDocument">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Info Dokumen</h4>
            </div>
            <div class="modal-body" id="bodyDetailDocument"></div>
        </div>
    </div>
</div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.tailwindcss.js"></script>

<script>
$(document).ready(function () {
    let table = $('#example').DataTable({
    ajax: {
        url: "{{ route('user.page.document.post.document') }}",
        type: "POST",
        data: {
            data: "{{ $dataDocument['th'] }}",
            _token: "{{ csrf_token() }}"
        },
        dataSrc: 'data'
    },
    columns: [
        {
            data: 'id_document',
            render: function (data) {
                return `<input type="checkbox" class="selectItem" value="${data}">`;
            },
            orderable: false,
        },
        {
            data: null,
            render: (data, type, row, meta) => meta.row + 1
        },
        { data: 'tanggal' },
        { data: 'nama_document' },
        {
            data: null,
            render: function (row) {
                var pathParts = row.direktory_document.split('/');
                var tahun = pathParts[2];
                var fileName = pathParts[3];

                var previewUrl = "{{ url('/document/preview') }}/" + tahun + "/" + fileName;
                var deleteUrl = "{{ url('/document/delete') }}/" + row.id_document;

                return `
                    <div class="flex items-center justify-center gap-2">
                       <a href="${previewUrl}" target="_blank" class="text-center px-4 text-[blue] no-underline cursor-pointer hover:bg-[#eaea] rounded-lg py-2">
                            <i class="fa fa-eye fa-lg block"></i>
                            <b class="block mt-1">Preview</b>
                        </a>
                       <button type="button" onclick="hapusDokumen('${deleteUrl}')" class="text-center px-4 text-[red] no-underline cursor-pointer hover:bg-[#eaea] rounded-lg py-2 mx-1">
                            <i class="fa fa-trash fa-lg block"></i>
                            <b class="block mt-1">Delete</b>
                        </button>
                        <a onclick="detailDocument(${row.id_document})" class="text-center px-4 text-[blue] cursor-pointer no-underline hover:bg-[#eaea] rounded-lg py-2" data-bs-toggle="modal" data-bs-target="#detailDocument">
                            <i class="fa fa-info-circle fa-lg block"></i>
                            <b class="block mt-1">Info</b>
                        </a>
                    </div>
                `;
            }
        }
    ]
});
    $('#dt-length-0').addClass('bg-white mr-4');
    $('#dt-search-0').addClass('bg-white text-gray-800 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring focus:ring-blue-300');
    $('#example thead tr').addClass('text-black');
    $(document).on('change', '#select-all', function () {
    $('.selectItem').prop('checked', $(this).is(':checked'));
});
    $('#hapusTerpilih').on('click', function () {
        const selected = $('.selectItem:checked').map(function () {
            return this.value;
        }).get();

        if (selected.length === 0) {
            Swal.fire('Tidak ada dokumen terpilih', '', 'warning');
            return;
        }

        Swal.fire({
            title: 'Yakin ingin menghapus dokumen terpilih?',
            text: `${selected.length} dokumen akan dihapus!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('user.page.document.massDelete') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: selected
                    },
                    success: function (response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });
});

function hapusDokumen(url) {
    Swal.fire({
        title: 'Hapus Dokumen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            let form = document.createElement('form');
            form.action = url;
            form.method = 'POST';

            let token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);

            let method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

function detailDocument(id) {
    $.post("{{ route('user.page.document.post.detail') }}", {
        _token: "{{ csrf_token() }}",
        data: id
    }, function (res) {
        if (res.status) {
            const d = res.data;
            const html = `
                <table class="table-auto w-full text-left">
                    <tr><th>Tanggal:</th><td>${d.tanggal.split('T')[0]}</td></tr>
                    <tr><th>Tahun:</th><td>${d.tahun}</td></tr>
                    <tr><th>Berkas:</th><td>${d.nama_document}</td></tr>
                    <tr><th>NPP:</th><td>${d.npp}</td></tr>
                    <tr><th>User:</th><td>${d.nama_user}</td></tr>
                    <tr><th>Status:</th><td>${d.status}</td></tr>
                </table>`;
            $('#bodyDetailDocument').html(html);
        } else {
            $('#bodyDetailDocument').html(`<div class="text-red-500">${res.message}</div>`);
        }
    }).fail(function () {
        $('#bodyDetailDocument').html('<div class="text-red-500">Terjadi kesalahan saat memuat detail</div>');
    });
}
</script>
@endpush
