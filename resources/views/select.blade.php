@extends('app')
@section('content')
    <div class="min-h-screen">
        <div class="grid grid-flow-col text-3xl">
            <div class="text-red-600 cursor-pointer">
                <i onclick="history.back()" class="fa-solid fa-caret-left p-3 rounded"></i>
                <span onclick="history.back()">Change Date</span>
            </div>
        </div>
        <div class="shadow border border-gray-100 m-3 p-3">
            <div class="px-6 mb-1">
                <div>วันที่เข้ารับการทดสอบ</div>
                <div class="text-2xl text-red-600">
                    {{ $date }}
                </div>
            </div>
            <div class="px-6 mb-1">
                <div>รูปแบบการทดสอบ</div>
                <div>
                    <div>
                        <input type="radio" id="Online" name="interview_type" value="Online" checked>
                        <label for="Online">Online</label>
                    </div>
                    <div>
                        <input type="radio" id="Offline" name="interview_type" value="Offline">
                        <label for="Offline">Offline (Face to Face)</label>
                    </div>
                </div>
            </div>
            <div class="px-6">
                <div>รอบการเข้าทดสอบ</div>
                @foreach ($data as $key => $tech)
                    <div class="col-span-2 font-bold p-3 border-2 border-green-300 text-green-600 rounded my-3 text-center"
                        onclick="showTeacher('#teacher{{ $tech['id'] }}')">
                        {{ $key }}
                    </div>
                    <div id="teacher{{ $tech['id'] }}">
                        @foreach ($tech['slot'] as $item)
                            <div
                                class="mb-1 grid grid-cols-2 @if ($item['time'] == 'Break') bg-gray-200 @endif @if ($item['active']) hover:bg-gray-100 @endif">
                                <div class="pt-3 ps-3">
                                    {{ $item['time'] }}
                                </div>
                                @if ($item['active'])
                                    <div class="text-end">
                                        <button onclick="select('{{ $item['id'] }}')"
                                            class="text-green-400 p-2 border border-green-400 hover:text-white hover:bg-green-400">Available</button>
                                    </div>
                                @else
                                    <div class="text-end">
                                        <button class="text-red-600 p-3 cursor-not-allowed">Not Available</button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function showTeacher(id) {
            $(id).toggle()
        }

        function select(id) {
            type = $('input[name="interview_type"]:checked').val()
            Swal.fire({
                    title: 'ยืนยันการเลือก',
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
                        formData.append('id', id);
                        formData.append('interview_type', type);
                        const res = await axios.post("{{ env('APP_URL') }}" + "/saveslot", formData, {
                            "Content-Type": "multipart/form-data"
                        });
                        if (res.data.status == 1) {
                            Swal.fire({
                                title: 'จองสำเร็จ',
                                text: res.data.text,
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
                        } else if (res.data.status == 3) {
                            Swal.fire({
                                title: 'ไม่สำเร็จ',
                                text: 'เวลาที่เลือกถูกจองไปเรียบร้อยแล้ว',
                                icon: 'error',
                                confirmButtonText: "ยืนยัน",
                                confirmButtonColor: 'red',
                            }).then(function(isConfirmed) {
                                if (isConfirmed) {
                                    window.location.reload();
                                }
                            })
                        }
                    }
                });
        }
    </script>
@endsection
