<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    @vite('resources/css/app.css')
</head>

<body class="">
    <div class="w-4/5 m-auto">
        <div class="grid grid-cols-7 gap-3 py-6">
            @foreach ($data as $key => $month)
                <div class="col-span-7 text-3xl font-bold text-center mb-3">{{ $key }}</div>
                <div class="text-center">Sunday</div>
                <div class="text-center">Monday</div>
                <div class="text-center">Tuesday</div>
                <div class="text-center">Wednesday</div>
                <div class="text-center">Thursday</div>
                <div class="text-center">Friday</div>
                <div class="text-center">Saturday</div>
                @foreach ($month as $i => $item)
                    @if ($i == 0 && $item->day == 'Mon')
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($i == 0 && $item->day == 'Tue')
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($i == 0 && $item->day == 'Wed')
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($i == 0 && $item->day == 'Thu')
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($i == 0 && $item->day == 'Fri')
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                        <div class="w-20 h-20 rounded bg-gray-50 m-auto"></div>
                    @endif
                    @if ($item->active)
                        <div
                            class="text-center pt-5 font-bold m-auto w-20 h-20 rounded text-3xl bg-green-100 cursor-pointer hover:bg-green-300">
                            <span class="m-auto">{{ $item->date }}</span>
                        </div>
                    @else
                        <div
                            class="text-center pt-5 font-bold m-auto w-20 h-20 rounded text-3xl bg-red-200 cursor-pointer hover:bg-green-300">
                            <span class="m-auto">{{ $item->date }}</span>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
</body>

</html>
