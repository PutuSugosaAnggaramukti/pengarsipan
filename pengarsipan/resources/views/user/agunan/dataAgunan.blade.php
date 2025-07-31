@extends('user.dashboard.app')

@section('title',"Agunan ".$dataAgunan["th"])

@push('link')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.tailwindcss.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('breadcurb')
    <a href="{{route('user.page.dashboard')}}" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]">Dashboard <i class="fa fa-dashboard"></i></a> &gt;
    <a href="{{route('user.page.agunan')}}" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]">Agunan <i class="fa fa-dashboard"></i></a> &gt;
    <a onclick="dataAgunan({{$dataAgunan['th']}})" class="p-3 text-white no-underline rounded-lg hover:bg-[#4681ff]" data-bs-toggle="modal" data-bs-target="#menuAgunan">
        {{$dataAgunan["th"]}}
    </a>
    <div class="modal" id="menuAgunan">
        <div class="modal-dialog w-full">
            <div class="modal-content">
                <div class="modal-header">
                    <b class="modal-title">Agunan</b>
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
                <th class="w-[100px]">No</th>
                <th>Tanggal</th>
                <th class="w-2/3">Nama Agunan</th>
                <th class="w-[220px] whitespace-nowrap">Option</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100 teble"></tbody>
        <tfoot>
            <tr>
                <td></td>
                <td data-filter="true"></td>
                <td data-filter="true"></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>
@endsection



<!-- Modal Detail -->
<div class="modal" id="detailAgunan">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Info Agunan</h4>
            </div>
            <div class="modal-body" id="bodyDetailAgunan">
                <!-- Data akan diinject via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal" id="editAgunan">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Delete Agunan</h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

@push('script')
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.tailwindcss.js"></script>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        showConfirmButton: false,
        timer: 3000
    });
</script>
@endif
@endpush


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
            Swal.fire('Tidak ada agunan terpilih', '', 'warning');
            return;
        }

        Swal.fire({
            title: 'Yakin ingin menghapus agunan terpilih?',
            text: `${selected.length} agunan akan dihapus!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('user.page.agunan.massDelete') }}",
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
        ajax:{
            url:"{{route('user.page.agunan.post.agunan')}}",
            type:"POST",
            data:{
                data:"{{$dataAgunan['th']}}",
                _token:"{{csrf_token()}}"
            },
            dataSrc: 'data'
        },
        columns:[
            {
                data: 'id_agunan',
                render:function(data, type, row){
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
            { data: 'nama_agunan', title: 'Nama Agunan' },
            { 
              data: null,
              render:function(data, type, row, meta){
                var pathParts = row.direktori_agunan.split('/');
                var tahun = pathParts[2];
                var fileName = pathParts[3];

                var previewUrl = "{{url('/agunan/preview')}}" + "/" + tahun + "/" + fileName;
                var deleteUrl = "{{url('/agunan/delete')}}" + "/" + row.id_agunan;

                return `
                    <div class="flex items-center justify-center gap-2 whitespace-nowrap">
                        <a href="${previewUrl}" target="_blank" class="text-center px-4 text-[blue] no-underline cursor-pointer hover:bg-[#eaea] rounded-lg py-2">
                            <i class="fa fa-eye fa-lg block"></i>
                            <b class="block mt-1">Preview</b>
                        </a>
                       <button type="button" onclick="hapusAgunan('${deleteUrl}')" class="text-center px-4 text-[red] no-underline cursor-pointer hover:bg-[#eaea] rounded-lg py-2 mx-1">
                            <i class="fa fa-trash fa-lg block"></i>
                            <b class="block mt-1">Delete</b>
                        </button>
                        <a onclick="detailAgunan(${row.id_agunan})" class="text-center px-4 text-[blue] cursor-pointer no-underline hover:bg-[#eaea] rounded-lg py-2" data-bs-toggle="modal" data-bs-target="#detailAgunan">
                            <i class="fa fa-info-circle fa-lg block"></i>
                            <b class="block mt-1">Info</b>
                        </a>
                    </div>
                `;
              },
              title:"Option"
            }
        ],
    });

    $('#dt-length-0').addClass('bg-white mr-4');
    $('#dt-search-0').addClass('bg-white text-gray-800 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring focus:ring-blue-300');
    $('#example thead tr').addClass('text-black');
});

function hapusAgunan(url) {
    Swal.fire({
        title: 'Hapus Agunan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
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

function detailAgunan(id_agunan) {
    $.ajax({
        url: "{{ route('user.page.agunan.post.detail') }}",
        type: "POST",
        dataType: "json",
        data: {
            data: id_agunan,
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {
            if(res.status){
                var html = `
                    <table class="table-auto w-full text-left">
                        <tr><th>Tanggal</th><td>${res.data.tanggal}</td></tr>
                        <tr><th>Tahun</th><td>${res.data.tahun}</td></tr>
                        <tr><th>Nama Agunan</th><td>${res.data.nama_agunan}</td></tr>
                        <tr><th>NPP</th><td>${res.data.npp}</td></tr>
                        <tr><th>Nama User</th><td>${res.data.nama_user}</td></tr>
                        <tr><th>Status</th><td>${res.data.status}</td></tr>
                    </table>
                `;
                $("#bodyDetailAgunan").html(html);
            } else {
                $("#bodyDetailAgunan").html(`<div class="text-red-500">${res.message}</div>`);
            }
        },
        error: function(xhr, status, error){
            console.error(xhr.responseText);
            $("#bodyDetailAgunan").html(`<div class="text-red-500">Terjadi kesalahan</div>`);
        }
    });
}
</script>
@endpush
