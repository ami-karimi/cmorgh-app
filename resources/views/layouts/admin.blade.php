<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'مدیریت کل | همراه سیمرغ' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{url('css/persian-datepicker.min.css')}}">
    @livewireStyles
    <style>
        .persian-datepicker-cheetah { background-color: #18181b !important; border: 1px solid #27272a !important; border-radius: 1rem !important; font-family: inherit !important; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5) !important; }
        .persian-datepicker-cheetah .datepicker-header { background: #27272a !important; }
        .persian-datepicker-cheetah .datepicker-plot-area .datepicker-day-view .table-days td span.other-month { color: #3f3f46 !important; }
        .persian-datepicker-cheetah .datepicker-plot-area .datepicker-day-view .table-days td.selected span { background: #f97316 !important; box-shadow: 0 0 10px rgba(249,115,22,0.5) !important; }
        .persian-datepicker-cheetah .datepicker-plot-area .datepicker-day-view .table-days td span:hover { background: #27272a !important; color: #fff !important; }
        .persian-datepicker-cheetah select { background: #27272a !important; color: white !important; border: none !important; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 antialiased font-sans selection:bg-orange-500/30 selection:text-orange-200" x-data="{ sidebarOpen: false }">


  @include('layouts.partials.admin.sidebar')
<div class="flex flex-col min-h-screen lg:mr-72 transition-all duration-300">

    @include('layouts.partials.admin.header')

    <main class="flex-1 p-6 lg:p-10">
        {{ $slot }}
    </main>

    <footer class="py-4 text-center border-t border-zinc-800/50 mt-auto">
        <p class="text-xs text-zinc-600 font-medium">© {{ date('Y') }} پنل مدیریت همراه سیمرغ. تمامی حقوق محفوظ است.</p>
    </footer>

</div>

@livewireScripts
  <script src="{{url('js/livewire-sortable.js')}}"></script>
  <script src="{{url('js/jquery-3.6.0.min.js')}}"></script>
  <script src="{{url('js/persian-date.min.js')}}"></script>
  <script src="{{url('js/persian-datepicker.min.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
