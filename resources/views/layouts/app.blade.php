<head>

  <title>{{ $title }}</title>

  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @livewireStyles
</head>

<body class="bg-slate-100">
  {{ $slot }}

  @livewireScripts
</body>