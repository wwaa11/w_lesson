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
        <div class="p-3 flex">
            <div onclick="addTeacher()"
                class=" p-3 font-bold rounded border border-green-400 text-green-400 hover:bg-green-400 hover:text-white cursor-pointer">
                <i class="fa-solid fa-plus"></i> <span id="teacher_text">New Teacher</span>
            </div>
        </div>
        <div class="p-3 shadow">
            <div class="font-bold text-3xl mb-3 px-3">Teacher</div>
            @foreach ($teachers as $item)
                <a href="{{ env('APP_URL') }}/admin/{{ $item->user }}">
                    <div class="p-3 shadow mb-3 cursor-pointer hover:bg-green-200">
                        <div>{{ $item->name }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            var authCookie = getCookie('auth');
            console.log(authCookie)
            if (authCookie) {
                $('#login').hide();
                $('#content').show();
            }
        });

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

        function addTeacher() {
            Swal.fire({
                    title: 'ระบุชื่อคุณครู',
                    icon: 'question',
                    html: `<input type="text" id="name" class="swal2-input" placeholder="Teacher Name">`,
                    confirmButtonText: "ยืนยัน",
                    confirmButtonColor: "green",
                    preConfirm: () => {
                        const name = Swal.getPopup().querySelector('#name').value
                        if (!name) {
                            Swal.showValidationMessage(`โปรดระบุชื่อ`)
                        }
                        return {
                            name: name,
                        }
                    }
                })
                .then(async (result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('name', result.value.name);
                        $('#teacher_text').html('Please Wait...')
                        const res = await axios.post("{{ env('APP_URL') }}" + "/addteacher", formData, {
                            "Content-Type": "multipart/form-data"
                        });
                        if (res.data.status == 1) {
                            Swal.fire({
                                title: 'เพิ่มสำเร็จ',
                                text: res.data.text,
                                icon: 'success',
                                confirmButtonColor: 'green'
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
