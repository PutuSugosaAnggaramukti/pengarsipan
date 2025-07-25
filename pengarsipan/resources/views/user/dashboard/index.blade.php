@extends('user.dashboard.app')

@section('title',"Dokumen")

@section('breadcurb')
    <a href="#" class="p-3 text-blue-600 hover:underline text-white">Dashboard <i class="fa fa-dashboard"></i></a> &gt;
@endsection

@section('content')

    <div class="grid sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">
        <a href="{{url('/User/Document')}}" class="rounded-lg">
            <div class="bg-[#0012cf] rounded-lg p-4 hover:bg-blue-500">
                <div class="flex flex-column items-center">
                    <i class="fa fa-book fa-2x text-[white]"></i>
                    <b class="text-white font-bold">Berkas</b>
                </div>
            </div>
        </a>
        <a href="{{ url('/User/Agunan') }}" class="rounded-lg">
            <div class="bg-[#0012cf] rounded-lg p-4 hover:bg-blue-500">
                <div class="flex flex-column items-center">
                    <i class="fas fa-file-archive fa-2x text-[white]"></i>
                    <b class="text-white font-bold">Agunan</b>
                </div>
            </div>
        </a>
         <a href="{{ route('user.recyclebin') }}" class="rounded-lg">
        <div class="bg-[#cf0000] rounded-lg p-4 hover:bg-red-600">
            <div class="flex flex-column items-center">
                <i class="fas fa-trash-alt fa-2x text-[white]"></i>
                <b class="text-white font-bold">Recycle Bin</b>
            </div>
        </div>
    </a>
    </div>
    
@endsection

@push('script')

    <script>
        $(document).ready(function () {
            $('input[type="text"]').on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $(".grid a").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
    
@endpush
