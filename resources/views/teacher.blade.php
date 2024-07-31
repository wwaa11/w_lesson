@extends('app')
@section('content')
    <div class="min-h-screen">
        <div class="p-3">
            <div class="text-3xl text-blue-400 font-bold">
                <i onclick="history.back()"
                    class="fa-solid fa-caret-left p-3 text-red-600 cursor-pointer bg-gray-200 w-10 h-14 rounded"></i>
                {{ $data['name'] }}
            </div>
            <div class="grid md:grid-cols-3">
                @foreach ($data['slot'] as $key => $day)
                    <div class="mb-3 shadow">
                        <div class="p-3" onclick="toggle('#{{ $key }}')">
                            <input @if ($day['active']) checked @endif type="checkbox"
                                onclick="dateCheck('{{ $key }}',{{ $day['active'] }})"> {{ $day['FullDate'] }}
                        </div>
                        <div id="{{ $key }}" class="grid grid-cols-3 gap-3 bg-gray-100 hidden">
                            @foreach ($day['time'] as $item)
                                <div class="p-3 col-span-1">
                                    <input onclick="timeChech('{{ $item['id'] }}',{{ $item['active'] }})"
                                        @if ($item['active']) checked @elseif($item['owner'] !== null) checked disabled @endif
                                        type="checkbox">
                                    <span>{{ $item['time'] }} </span>
                                    @if ($item['owner'] !== null)
                                        <span class="text-red-500">( {{ $item['owner'] }} )</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        async function dateCheck(date, check) {
            const formData = new FormData();
            formData.append('user', '{{ $data['user'] }}');
            formData.append('date', date);
            formData.append('check', check);
            const res = await axios.post("{{ env('APP_URL') }}" + "/updateslot", formData, {
                "Content-Type": "multipart/form-data"
            });
            if (res.data.status == 1) {
                window.location.reload();
            }
        }
        async function timeChech(id, check) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('check', check);
            const res = await axios.post("{{ env('APP_URL') }}" + "/updatetime", formData, {
                "Content-Type": "multipart/form-data"
            });
            if (res.data.status == 1) {
                window.location.reload();
            }
        }

        function toggle(id) {
            if ($(id).hasClass('hidden')) {
                $(id).removeClass('hidden')
            } else {
                $(id).addClass('hidden')
            }
        }
    </script>
@endsection
