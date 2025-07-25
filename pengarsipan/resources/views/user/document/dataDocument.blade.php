@extends('user.dashboard.app')

@section('title', "Document ".$dataDocument["th"])

@push('link')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.tailwindcss.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('breadcurb')
    <a href="{{route('user.page.dashboard')}}" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]">Dashboard <i class="fa fa-dashboard"></i></a> &gt;
    <a href="{{route('user.page.document')}}" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]">Document <i class="fa fa-dashboard"></i></a> &gt;
    <a onclick="dataDokument({{$dataDocument['th']}})" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]" data-bs-toggle="modal" data-bs-target="#menuDocument">
        {{$dataDocument["th"]}}
    </a>
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
                    <th class="w-[100px]">No</th>
                    <th class="hidden">Nomor</th>
                    <th>Tanggal</th>
                    <th class="w-2/3">Nama Document</th>
                    <th class="w-[220px] whitespace-nowrap">Option</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100"></tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td class="hidden"></td>
                    <td data-filter="true"></td>
                    <td data-filter="true"></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>

    <!-- Modal Detail Berkas -->
    <div class="modal fade" id="detailBerkas" tabindex="-1" aria-labelledby="detailBerkasLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detailBerkasLabel">Info Berkas</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="bodyDetailDocument">
                    <!-- Data di-inject oleh JS -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<!-- Bootstrap JS Bundle with Popper (WAJIB untuk modal Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.tailwindcss.js"></script>

<script>
  // Checkbox Select All
    $(document).on('change', '#selectAll', function () {
        $('.selectItem').prop('checked', $(this).is(':checked'));
    });

    // Tombol Hapus Terpilih
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
                        $('#example').DataTable().ajax.reload(); // reload datatable
                    },
                    error: function () {
                        Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });


$(document).ready(function () {
    $('#example').DataTable({
        responsive: true,
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
                render: function (data, type, row) {
                    return `<input type="checkbox" class="selectItem" value="${data}">`;
                },
                orderable: false,
                title: `<input type="checkbox" id="selectAll">`
            },
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                title: "No"
            },
            { data: 'nomor', visible: false },
            { data: 'tanggal', title: 'Tanggal' },
            { data: 'nama_document', title: 'Nama Document' },
            {
                data: null,
                render: function (data, type, row, meta) {
                    var pathParts = row.direktory_document.split('/');
                    var tahun = pathParts[2];
                    var fileName = pathParts[3];

                    var previewUrl = "{{ url('/document/preview') }}/" + tahun + "/" + fileName;
                    var deleteUrl = "{{ url('/document/delete') }}/" + row.id_document;

                    return `
                        <div class="flex items-center justify-center gap-2 whitespace-nowrap" >
                            <a href="${previewUrl}" target="_blank" class="text-center px-4 text-[blue] no-underline cursor-pointer hover:bg-[#eaea] rounded-lg py-2">
                                <i class="fa fa-eye fa-lg block"></i>
                                <b class="block mt-1">Preview</b>
                            </a>
                            <button type="button" onclick="hapusData('${deleteUrl}')" class="text-center px-4 text-[red] no-underline cursor-pointer hover:bg-[#eaea] rounded-lg py-2 mx-1">
                                <i class="fa fa-trash fa-lg block"></i>
                                <b class="block mt-1">Delete</b>
                            </button>
                            <a onclick="detailBerkas(${row.id_document})" class="text-center px-4 text-[blue] cursor-pointer no-underline hover:bg-[#eaea] rounded-lg py-2" data-bs-toggle="modal" data-bs-target="#detailBerkas">
                                <i class="fa fa-info-circle fa-lg block"></i>
                                <b class="block mt-1">Info</b>
                            </a>
                        </div>
                    `;
                },
                title: "Option"
            }
        ],

    });

    // Styling untuk input filter
    $('#dt-length-0').addClass('bg-white mr-4');
    $('#dt-search-0').addClass('bg-white text-gray-800 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring focus:ring-blue-300');
    $('#example thead tr').addClass('text-black');
});

function hapusData(url) {
    Swal.fire({
        title: 'Yakin ingin menghapus file ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Buat form sementara dan submit
            let form = $('<form>', {
                method: 'POST',
                action: url
            });

            let token = $('<input>', {
                type: 'hidden',
                name: '_token',
                value: '{{ csrf_token() }}'
            });

            let method = $('<input>', {
                type: 'hidden',
                name: '_method',
                value: 'DELETE'
            });

            form.append(token, method);
            $('body').append(form);
            form.submit();
        }
    });
}


function detailBerkas(id) {
    $.ajax({
        url: "{{ route('user.page.document.post.detail') }}",
        type: "POST",
        dataType: "json",
        data: {
            data: id,
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {
            if (res.status) {
                var data = res.data;
                var html = `
                    <div>
                        <p><b>Nomor:</b> ${data.nomor}</p>
                        <p><b>Tanggal Upload:</b> ${data.tanggal}</p>
                        <p><b>Tahun:</b> ${data.tahun}</p>
                        <p><b>Nama Dokumen:</b> ${data.nama_document}</p>
                        <p><b>NPP:</b> ${data.npp}</p>
                        <p><b>Nama User:</b> ${data.nama_user}</p>
                        <p><b>Status:</b> ${data.status}</p>
                    </div>
                `;
                $("#bodyDetailDocument").html(html);
            } else {
                $("#bodyDetailDocument").html(`<p class="text-red-500">Data tidak ditemukan</p>`);
            }
        },
        error: function (xhr) {
            console.error("AJAX Error:", xhr.responseText);
            $("#bodyDetailDocument").html(`<p class="text-red-500">Gagal mengambil data.</p>`);
        }
    });
}
</script>
@endpush
