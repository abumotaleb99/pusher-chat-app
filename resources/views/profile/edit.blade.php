@extends('app')
@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Profile</h1>
            <a href="{{ route('chat.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition duration-300">Back to Chat</a>
        </div>
        <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype='multipart/form-data'>
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-bold mb-2">Username</label>
                <input type="text" id="username" name="username" value="{{ $user->username }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="first_name" class="block text-gray-700 font-bold mb-2">First name</label>
                <input type="text" id="first_name" name="first_name" value="{{ $user->first_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-gray-700 font-bold mb-2">Last name</label>
                <input type="text" id="last_name" name="last_name" value="{{ $user->last_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4 hidden">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <!-- Avatar preview -->
                @if ($user->profile_photo)
                <div class="">
                    <div class="mt-4 flex justify-center">
                        <img id="avatarPreview" src="{{ asset($user->profile_photo) }}" alt="Avatar Preview" class="w-20 h-20 rounded-full object-cover border-2 border-gray-300">
                    </div>
                </div>
                @endif
                <label for="avatar" class="block text-gray-700 font-bold mb-2">Avatar</label>
                <input type="file" id="avatar" name="profile_photo" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300">Save Changes</button>
                <button type="reset" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 transition duration-300">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection
