@extends('app')
@section('content')
    <div id="login" class="min-h-screen mx-auto p-3 flex flex-col">
        <div class="mb-3 mx-auto mt-60 shadow p-6">
            <div class="mb-3 text-blue-600 font-bold text-center text-3xl">
                Sign in
            </div>
            <input class="bg-gray-50 border border-gray-200 rounded p-3 w-full mb-3" id="userid" type="text">
            <input class="bg-gray-50 border border-gray-200 rounded p-3 w-full mb-3" id="password" type="password">
            <button onclick="login()"
                class="w-full text-blue-400 border border-blue-400 hover:bg-blue-400 hover:text-white p-3 rounded ">Login</button>
        </div>
    </div>
    <div id="content" class="min-h-screen p-3 hidden">
        <div class="p-3 flex gap-6">
            <div class="flex-grow"></div>
            <div onclick="addSlot()"
                class="p-3 font-bold rounded border border-green-400 text-green-400 hover:bg-green-400 hover:text-white cursor-pointer">
                <i class="fa-solid fa-plus"></i> <span id="teacher_text">Add Date</span>
            </div>
        </div>
        <div class="p-3 shadow">
            <div class="font-bold text-3xl mb-3 px-3">All Slot</div>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                @foreach ($outPut as $key => $date)
                    <div class="shadow mb-3">
                        <div onclick="toggleKey('#{{ str_replace(' ', '', $key) }}')" class="text-center bg-gray-200 p-3">
                            {{ $key }}</div>
                        <div class="hidden" id="{{ str_replace(' ', '', $key) }}">
                            @foreach ($date as $time => $Timedata)
                                <div class="my-1 rounded border">
                                    <div class="p-3 text-center bg-gray-100">{{ $time }}</div>
                                    @foreach ($Timedata as $item)
                                        @if (!$item['active'])
                                            <div class="flex hover:bg-blue-400">
                                                <div class="flex-shrink p-3">{{ $item['owner'] }}</div>
                                                <div class="flex-grow p-3">
                                                    {{ $item['owner_name'] }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
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
        $(document).ready(function() {
            var authCookie = getCookie('auth');
            if (authCookie) {
                $('#login').hide();
                $('#content').show();
            }
        });

        const show = false;

        function toggleTime(id) {
            // if (!show) {
            //     $(id).removeClass('hidden');
            //     $(id).addClass('flex');
            //     show = true;
            // } else {
            //     $(id).addClass('hidden');
            //     $(id).removeClass('flex');
            //     show = false;
            // }
        }

        function toggleKey(id) {
            $(id).toggle()
        }

        async function login() {
            const formData = new FormData();
            formData.append('userid', $('#userid').val());
            formData.append('password', $('#password').val());
            const res = await axios.post("{{ env('APP_URL') }}" + "/auth", formData, {
                "Content-Type": "multipart/form-data"
            });
            console.log(res)
            if (res.data.status == 1) {
                $('#login').hide();
                setCookie('auth', $('#userid').val(), 1);
                $('#content').show();
            } else if (res.data.status == 2) {
                Swal.fire({
                    title: 'รหัสผ่านไม่ถูกต้อง',
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: 'red'
                })
            }
        }

        async function addSlot() {
            const swal = await Swal.fire({
                title: 'Title',
                icon: 'question',
                html: '<div class="flex gap-3"><input class="flex-grow p-3 border border-blue-600 rounded" value="2025-01-27" type="date" id="date_start" ><input class="flex-grow p-3 border border-blue-600 rounded" type="date" id="date_end" value="2025-04-02"></div>',
                preConfirm: false,
                preConfirm: () => {
                    return [
                        $('#date_start').val(),
                        $('#date_end').val()
                    ]
                }
            })
            if (swal.isConfirmed) {
                Swal.fire({
                    title: 'Please, wait.',
                    icon: 'info',
                })
                const formData = new FormData();
                formData.append('date', swal.value);
                const res = await axios.post("{{ env('APP_URL') }}" + "/addslot", formData);
                console.log(res.data.status);
                if (res.data.status == 'success') {
                    Swal.fire({
                        title: 'Success.',
                        icon: 'success',
                    })
                    location.reload()
                }

            }
        }
    </script>
@endsection
