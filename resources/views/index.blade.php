@extends('app')
@section('content')
    <div class="w-4/5 m-auto">
        <div class="p-3 md:text-3xl font-bold text-center">English Interview</div>
        <div class="mt-6 border-2 border-gray-400 rounded p-3 shadow-md">
            <div class="ps-3 mb-1 flex">
                <div class="flex-grow font-bold text-lg">รอบการเข้าทดสอบของฉัน</div>
                <div class="flex-shrink text-red-600 cursor-pointer font-bold" onclick="login()">เข้าสู่ระบบ</div>
            </div>
            <div class="" id="loadData"></div>
        </div>
        <hr class="my-3">
        <div class="p-3 font-bold">เลือกวันที่ต้องการเข้ารับการทดสอบ</div>
        @foreach ($data as $key => $month)
            <div class="grid md:grid-cols-7 gap-3 mb-3 shadow p-6">
                <div
                    class="md:col-span-7 text-3xl font-bold text-center mb-3 p-1 cursor-pointer border-2 border-gray-800 text-gray-800">
                    {{ $key }}
                </div>
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
                            class="text-center pt-5 font-bold m-auto w-full md:w-20 h-20 rounded text-3xl border-2 border-green-400 text-green-400 cursor-pointer hover:bg-green-400 hover:text-white">
                            <div class="m-auto grid grid-flow-col">
                                <div class="md:hidden text-start ps-6">{{ $item->day }}</div>
                                <div class="text-end pe-6 md:pe-0 md:text-center">{{ $item->date }}</div>
                            </div>
                        </div>
                    @else
                        <div
                            class="text-center pt-5 font-bold m-auto w-full md:w-20 h-20 rounded text-3xl bg-gray-200 text-gray-800 cursor-not-allowed">
                            <div class="m-auto grid grid-flow-col">
                                <div class="md:hidden text-start ps-6">{{ $item->day }}</div>
                                <div class="text-end pe-6 md:pe-0 md:text-center">{{ $item->date }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var authCookie = getCookie('auth');
            if (authCookie) {
                viewData(authCookie)
            }
        });

        async function login() {
            Swal.fire({
                    title: 'เข้าสู่ระบบ',
                    icon: 'question',
                    html: `<input type="text" id="login" class="swal2-input" placeholder="รหัสพนักงาน"><input type="password" id="password" class="swal2-input" placeholder="รหัสผ่าน">`,
                    confirmButtonText: "ยืนยัน",
                    confirmButtonColor: "green",
                    preConfirm: () => {
                        const login = Swal.getPopup().querySelector('#login').value
                        const password = Swal.getPopup().querySelector('#password').value
                        if (!login || !password) {
                            Swal.showValidationMessage(`โปรดระบุรหัสพนักงาน และ รหัสผ่าน`)
                        }
                        return {
                            login: login,
                            password: password
                        }
                    }
                })
                .then(async (result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('userid', result.value.login);
                        formData.append('password', result.value.password);
                        const res = await axios.post("{{ env('APP_URL') }}" + "/auth", formData, {
                            "Content-Type": "multipart/form-data"
                        });
                        if (res.data.status == 1) {
                            Swal.fire({
                                title: 'Login Success.',
                                icon: 'success',
                                confirmButtonColor: 'green'
                            }).then(function(isConfirmed) {
                                if (isConfirmed) {
                                    setCookie('auth', result.value.login, 30);
                                    window.location.href = "{{ env('APP_URL') }}/";
                                }
                            })
                        } else if (res.data.status == 2) {
                            Swal.fire({
                                title: 'รหัสผ่านไม่ถูกต้อง',
                                icon: 'error',
                                confirmButtonText: "ยืนยัน",
                                confirmButtonColor: 'red',
                            })
                        }
                    }
                });
        }

        async function viewData(user) {
            const formData = new FormData();
            formData.append('userid', user);
            const res = await axios.post("{{ env('APP_URL') }}" + "/viewslot", formData, {
                "Content-Type": "multipart/form-data"
            });
            $('#loadData').html(res.data.data)

        }
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
