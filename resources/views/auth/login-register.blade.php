@extends('app')
@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Welcome</h2>
</div>

<div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-6 lg:px-8">
    <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div class="flex justify-center mb-6">
            <button id="loginTab" class="px-4 py-2 font-medium text-sm bg-indigo-600 text-white rounded-l-md focus:outline-none">Login</button>
            <button id="registerTab" class="px-4 py-2 font-medium text-sm bg-gray-200 text-gray-700 rounded-r-md focus:outline-none">Register</button>
        </div>

        <!-- Login Form -->
        <form id="loginForm" class="space-y-6" action="{{ route('auth.login.submit') }}" method="POST">
            @csrf
            <div>
                <label for="loginEmail" class="block text-sm font-medium text-gray-700">Email address</label>
                <div class="mt-1">
                    <input id="loginEmail" name="email" type="email" autocomplete="email" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            <div>
                <label for="loginPassword" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1">
                    <input id="loginPassword" name="password" type="password" autocomplete="current-password" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                </div>
                <!-- <div class="text-sm">
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Forgot your password?
                    </a>
                </div> -->
            </div>
            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign in
                </button>
            </div>
        </form>

        <!-- Register Form -->
        <form id="registerForm" class="space-y-6 hidden" action="{{ route('auth.register.submit') }}" method="POST">
            @csrf
            <div>
                <label for="registerUsername" class="block text-sm font-medium text-gray-700">Username</label>
                <div class="mt-1">
                    <input id="registerUsername" name="username" type="text" autocomplete="username" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            <div>
                <label for="registerFirstName" class="block text-sm font-medium text-gray-700">First name</label>
                <div class="mt-1">
                    <input id="registerFirstName" name="first_name" type="text" autocomplete="given-name" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            <div>
                <label for="registerLastName" class="block text-sm font-medium text-gray-700">Last name</label>
                <div class="mt-1">
                    <input id="registerLastName" name="last_name" type="text" autocomplete="family-name" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            <div>
                <label for="registerEmail" class="block text-sm font-medium text-gray-700">Email address</label>
                <div class="mt-1">
                    <input id="registerEmail" name="email" type="email" autocomplete="email" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            <div>
                <label for="registerPassword" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1">
                    <input id="registerPassword" name="password" type="password" autocomplete="new-password" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Register
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Function to change tab and store the selection in localStorage
    function switchTab(tab) {
        if (tab === 'login') {
            loginTab.classList.add('bg-indigo-600', 'text-white');
            loginTab.classList.remove('bg-gray-200', 'text-gray-700');
            registerTab.classList.add('bg-gray-200', 'text-gray-700');
            registerTab.classList.remove('bg-indigo-600', 'text-white');
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
            localStorage.setItem('selectedTab', 'login');
        } else {
            registerTab.classList.add('bg-indigo-600', 'text-white');
            registerTab.classList.remove('bg-gray-200', 'text-gray-700');
            loginTab.classList.add('bg-gray-200', 'text-gray-700');
            loginTab.classList.remove('bg-indigo-600', 'text-white');
            registerForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
            localStorage.setItem('selectedTab', 'register');
        }
    }

    // Event listeners for the tabs
    loginTab.addEventListener('click', () => switchTab('login'));
    registerTab.addEventListener('click', () => switchTab('register'));

    // On page load, check localStorage and apply the selected tab
    window.onload = function() {
        // Check if the user has a saved tab
        const selectedTab = localStorage.getItem('selectedTab');
        if (selectedTab === 'register') {
            switchTab('register');
        } else {
            switchTab('login');
        }
    };
</script>
@endpush