<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messenger App</title>
  @stack('styles')
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom styles for dialog */
    dialog::backdrop {
      background-color: rgba(0, 0, 0, 0.5);
    }
  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
  @yield('content')

  @include('components.toaster')
  @stack('scripts')
  <script>
    // Show toast messages for validation or session messages
    @foreach ($errors->all() as $error)
      addToast('error', '{{ $error }}');
    @endforeach

    @if(session('success'))
      addToast('success', '{{ session('success') }}');
    @endif
                
    @if(session('error'))
      addToast('error', '{{ session('error') }}');
    @endif
  </script>
</body>
</html>