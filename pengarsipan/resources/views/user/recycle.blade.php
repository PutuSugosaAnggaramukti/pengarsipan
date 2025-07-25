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

    <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
       @foreach ($documents as $doc)
    <div class="bg-white shadow-lg rounded-lg p-4">
        <h3 class="font-bold text-lg text-blue-600">Berkas</h3>
                <p><b>Tahun:</b> {{ $doc->tahun }}</p>
                <p><b>Nama:</b> {{ $doc->nama_document }}</p>
        <div class="flex mt-3 space-x-2">
            <form action="{{ route('user.recyclebin.restore.document', $doc->id_document) }}" method="GET">
                <button type="submit" class="px-3 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    <i class="fa fa-undo"></i> Restore
                </button>
            </form>
            <form id="deleteForm-doc-{{ $doc->id_document }}" action="{{ route('user.recyclebin.forceDelete.document', $doc->id_document) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete('doc', {{ $doc->id_document }})" class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                    <i class="fa fa-trash"></i> Hapus Permanen
                </button>
            </form>
        </div>
    </div>
@endforeach

@foreach ($agunans as $agunan)
    <div class="bg-white shadow-lg rounded-lg p-4">
         <h3 class="font-bold text-lg text-purple-600">Agunan</h3>
                <p><b>Tahun:</b> {{ $agunan->tahun }}</p>
                <p><b>Nama:</b> {{ $agunan->nama_agunan }}</p>
        <div class="flex mt-3 space-x-2">
            <form action="{{ route('user.recyclebin.restore.agunan', $agunan->id_agunan) }}" method="GET">
                <button type="submit" class="px-3 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    <i class="fa fa-undo"></i> Restore
                </button>
            </form>
            <form id="deleteForm-agunan-{{ $agunan->id_agunan }}" action="{{ route('user.recyclebin.forceDelete.agunan', $agunan->id_agunan) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete('agunan', {{ $agunan->id_agunan }})" class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                    <i class="fa fa-trash"></i> Hapus Permanen
                </button>
            </form>
        </div>
    </div>
@endforeach

@push('script')
<script>
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
@endif
</script>
@endpush
@endsection
