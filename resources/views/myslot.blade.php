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
    <div id="content" class="min-h-screen">
        <div class="p-3">
            <div onclick="history.back()" class="text-red-600 cursor-pointer">
                <i class="fa-solid fa-caret-left"></i> Back
            </div>
            <div class="text-end text-red-600 cursor-pointer mb-3" onclick="signout()">Sign out</div>
            <div class="p-3 shadow flex flex-col text-center text-3xl" id="loadData">

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
                viewData(authCookie)
            }
        });
        async function viewData(user) {
            const formData = new FormData();
            formData.append('userid', user);
            const res = await axios.post("{{ env('APP_URL') }}" + "/viewslot", formData, {
                "Content-Type": "multipart/form-data"
            });
            $('#loadData').html(res.data.data)

        }
        async function login() {
            const formData = new FormData();
            formData.append('userid', $('#userid').val());
            formData.append('password', $('#password').val());
            const res = await axios.post("{{ env('APP_URL') }}" + "/auth", formData, {
                "Content-Type": "multipart/form-data"
            });
            if (res.data.status == 1) {
                $('#login').hide();
                setCookie('auth', $('#userid').val(), 30);
                viewData($('#userid').val())
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

        function signout() {
            eraseCookie('auth')
            window.location.reload();
        }
    </script>
@endsection
