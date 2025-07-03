@extends('user.dashboard.app')

@section('title',"Dokumen")

@section('breadcurb')
    <a href="#" class="text-blue-600 hover:underline">Dashboard <i class="fa fa-dashboard"></i></a> &gt;
@endsection

@section('content')
    
    <div class="pb-3 flex flex-row items-center">
        <label for="">Search : </label>
        <input type="text" class="w-26 rounded-lg h-10 border-2 ml-2 p-3">
    </div>
    
    <div class="grid sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @for ($i = 0; $i < count($dataBerkas); $i++)
            <a href="{{ route('user.page.document', ['parameter' => $dataBerkas[$i]["tahun"]]) }}" class="rounded-lg">
                <div class="bg-[#0012cf] rounded-lg p-4 hover:bg-blue-500">
                    <div class="flex flex-column items-center">
                        <i class="fa fa-book fa-2x text-[white]"></i>
                        <b class="text-white font-bold">{{ $dataBerkas[$i]["tahun"] }}</b>
                    </div>
                </div>
            </a>    
        @endfor
    </div>
    
@endsection

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
