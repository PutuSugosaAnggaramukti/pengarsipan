@extends('user.dashboard.app')

@section('title', "Recycle Bin")

@push('link')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('breadcurb')
    <a href="{{ route('user.page.dashboard') }}" class="p-3 text-blue-600 hover:underline text-white">
        Dashboard <i class="fa fa-dashboard"></i>
    </a> &gt;
    <a href="{{ route('user.recyclebin') }}" class="p-3 text-blue-600 hover:underline text-white">
        Recycle Bin</i>
    </a> &gt;
@endsection

@section('content')
    <h2 class="text-xl font-bold mb-4">Recycle Bin</h2>

    @if($documents->isEmpty() && $agunans->isEmpty())
        <div class="p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
            Tidak ada data yang terhapus.
        </div>
    @endif

      @if(!$documents->isEmpty() || !$agunans->isEmpty())
<form id="bulkActionForm" method="POST">
    @csrf
    {{-- <input type="hidden" name="_method" id="bulkMethod"> --}}

    {{-- Top bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
        <label class="inline-flex items-center">
            <input type="checkbox" id="selectAll" class="mr-2">
            <span class="font-semibold">Pilih Semua</span>
        </label>
        <div class="space-x-2">
            <button type="button" onclick="submitBulk('restore')" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <i class="fa fa-undo"></i> Restore Terpilih
            </button>
            <button type="button" onclick="submitBulk('delete')" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                <i class="fa fa-trash"></i> Hapus Permanen Terpilih
            </button>
        </div>
    </div>

    {{-- Grid --}}
    <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($documents as $doc)
        <div class="bg-white shadow-lg rounded-lg p-4 relative">
            <input type="checkbox" name="selected_ids[]" value="document-{{ $doc->id_document }}" class="absolute top-2 left-2 checkbox-item">
            <h3 class="font-bold text-lg text-blue-600 pl-6">Berkas</h3>
            <p><b>Tahun:</b> {{ $doc->tahun }}</p>
            <p><b>Nama:</b> {{ $doc->nama_document }}</p>
            <div class="flex mt-3 space-x-2 pl-6">
                {{-- Gunakan tombol biasa atau a href untuk tombol individu --}}
                <a href="{{ route('user.recyclebin.restore.document', $doc->id_document) }}" class="px-3 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    <i class="fa fa-undo"></i> Restore
                </a>
                <button type="button" onclick="confirmDelete('doc', {{ $doc->id_document }})" class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                    <i class="fa fa-trash"></i> Hapus Permanen
                </button>
                <form id="deleteForm-doc-{{ $doc->id_document }}" action="{{ route('user.recyclebin.forceDelete.document', $doc->id_document) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
        @endforeach

        @foreach ($agunans as $agunan)
        <div class="bg-white shadow-lg rounded-lg p-4 relative">
            <input type="checkbox" name="selected_ids[]" value="agunan-{{ $agunan->id_agunan }}" class="absolute top-2 left-2 checkbox-item">
            <h3 class="font-bold text-lg text-purple-600 pl-6">Agunan</h3>
            <p><b>Tahun:</b> {{ $agunan->tahun }}</p>
            <p><b>Nama:</b> {{ $agunan->nama_agunan }}</p>
            <div class="flex mt-3 space-x-2 pl-6">
                <a href="{{ route('user.recyclebin.restore.agunan', $agunan->id_agunan) }}" class="px-3 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    <i class="fa fa-undo"></i> Restore
                </a>
                <button type="button" onclick="confirmDelete('agunan', {{ $agunan->id_agunan }})" class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                    <i class="fa fa-trash"></i> Hapus Permanen
                </button>
                <form id="deleteForm-agunan-{{ $agunan->id_agunan }}" action="{{ route('user.recyclebin.forceDelete.agunan', $agunan->id_agunan) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
        @endforeach
    </div>
</form>
@endif


@push('script')
<script>
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.checkbox-item').forEach(cb => cb.checked = this.checked);
});

function submitBulk(actionType) {
    const selected = [...document.querySelectorAll('.checkbox-item:checked')].map(cb => cb.value);
    if (selected.length === 0) {
        Swal.fire('Pilih data terlebih dahulu!', '', 'warning');
        return;
    }

    Swal.fire({
        title: actionType === 'delete' ? 'Hapus permanen data terpilih?' : 'Restore data terpilih?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, lanjutkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('bulkActionForm');

            // Hapus input _method jika ada sebelumnya
            const oldMethodInput = document.querySelector('input[name="_method"]');
            if (oldMethodInput) oldMethodInput.remove();

            if (actionType === 'delete') {
                form.action = "{{ route('user.recyclebin.bulkDelete') }}";

                // Tambahkan input _method secara dinamis hanya saat delete
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
            } else {
                form.action = "{{ route('user.recyclebin.bulkRestore') }}";
                // Tidak menambahkan _method saat restore
            }

            form.submit();
        }
    });
}



function confirmDelete(type, id) {
    Swal.fire({
        title: 'Yakin hapus permanen?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm-' + type + '-' + id).submit();
        }
    });
}

function confirmDelete(type, id) {
    Swal.fire({
        title: 'Yakin hapus permanen?',
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm-' + type + '-' + id).submit();
        }
    })
}


// Notifikasi sukses setelah hapus
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    timer: 2000,
    showConfirmButton: false
});

document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('.checkbox-item').forEach(cb => cb.checked = this.checked);
});

@endif
</script>
@endpush
@endsection
