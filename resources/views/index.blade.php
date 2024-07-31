@extends('app')
@section('content')
    <div class="w-4/5 m-auto">
        <div class="p-3 md:text-3xl font-bold text-center bg-gray-50">เลือกวันที่ต้องการเข้ารับการทดสอบ</div>
        <a href="{{ env('APP_URL') }}/myslot">
            <div class="text-center text-2xl text-red-500">My Appointment</div>
        </a>
        <div class="grid md:grid-cols-7 gap-3 py-6">
            @foreach ($data as $key => $month)
                <div class="md:col-span-7 text-3xl font-bold text-center mb-3 bg-blue-300 p-3">{{ $key }}</div>
                <div class="text-center hidden md:block">Sunday</div>
                <div class="text-center hidden md:block">Monday</div>
                <div class="text-center hidden md:block">Tuesday</div>
                <div class="text-center hidden md:block">Wednesday</div>
                <div class="text-center hidden md:block">Thursday</div>
                <div class="text-center hidden md:block">Friday</div>
                <div class="text-center hidden md:block">Saturday</div>
                @foreach ($month as $i => $item)
                    @if ($i == 0 && $item->day == 'Mon')
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($i == 0 && $item->day == 'Tue')
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($i == 0 && $item->day == 'Wed')
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($i == 0 && $item->day == 'Thu')
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($i == 0 && $item->day == 'Fri')
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="hidden md:block w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($item->active)
                        <div onclick="selectDate('{{ $item->fulldate }}')"
                            class="text-center pt-5 font-bold m-auto w-full md:w-20 h-20 rounded text-3xl bg-green-100 cursor-pointer hover:bg-green-300">
                            <div class="m-auto grid grid-flow-col">
                                <div class="md:hidden text-start ps-6">{{ $item->day }}</div>
                                <div class="text-end pe-6 md:pe-0 md:text-center">{{ $item->date }}</div>
                            </div>
                        </div>
                    @else
                        <div
                            class="text-center pt-5 font-bold m-auto w-full md:w-20 h-20 rounded text-3xl bg-red-200 cursor-not-allowed">
                            <div class="m-auto grid grid-flow-col">
                                <div class="md:hidden text-start ps-6">{{ $item->day }}</div>
                                <div class="text-end pe-6 md:pe-0 md:text-center">{{ $item->date }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function selectDate(date) {
            const formData = new FormData();
            formData.append('date', date);
            const res = await axios.post("{{ env('APP_URL') }}" + "/check", formData, {
                "Content-Type": "multipart/form-data"
            });
            if (res.data.status == 1) {
                window.location = "{{ env('APP_URL') }}" + "/select/" + date
            } else if (res.data.status == 2) {
                Swal.fire({
                    title: 'วันที่เลือกเต็มแล้ว',
                    icon: 'error',
                }).then(function(isConfirmed) {
                    if (isConfirmed) {
                        window.location.reload();
                    }
                })
            }
        }
    </script>
@endsection
