@extends('app')
@section('content')
<div class="flex flex-col sm:flex-row h-screen overflow-hidden">
    <!-- Sidebar -->
    <div id="sidebar" class="w-full sm:w-80 bg-white border-r border-gray-200 flex flex-col h-screen sm:h-auto">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Messages</h1>
            <div class="flex items-center">
                <button id="toggleChat" class="sm:hidden bg-blue-500 text-white p-2 rounded-full mr-2">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </button>
                <div class="relative">
                    <button id="profileDropdown" class="bg-gray-200 rounded-full p-2 focus:outline-none">
                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </button>
                    <div id="dropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                        <div class="px-4 py-2 border-b border-gray-200 flex items-center">
                            <div class="w-8 h-8 bg-gray-300 rounded-full mr-3"></div>
                            <span class="font-medium text-gray-800">{{ auth()->user()->first_name .' '. auth()->user()->last_name }}</span>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit Profile</a>
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="relative">
                <input type="text" id="userSearch" placeholder="Search" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto" id="userList">
            <!-- User list will be populated by JavaScript -->
        </div>
        <div class="p-4 border-t border-gray-200">
            <button id="newMessageBtn" class="w-full bg-blue-500 text-white py-2 px-4 rounded-full hover:bg-blue-600 transition duration-150 ease-in-out flex items-center justify-center">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span id="newMessageBtnText">New Message</span>
            </button>
        </div>
    </div>

    <!-- Chat Area -->
    <div id="chatArea" class="hidden sm:flex flex-1 flex-col bg-white">
        <div id="chatHeader" class="p-4 border-b border-gray-200 flex items-center">
            <div class="w-10 h-10 bg-gray-300 rounded-full mr-3"></div>
            <h2 class="text-xl font-semibold text-gray-800">Select a chat to start messaging</h2>
        </div>
        <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Chat messages will be populated by JavaScript -->
            <div id="emptyStateMessage" class="flex items-center justify-center h-full">
                <p class="text-gray-500 text-lg">Select a conversation to start chatting</p>
            </div>
        </div>
        <div id="chatInput" class="p-4 border-t border-gray-200 hidden">
            <div class="flex items-center">
                <input type="text" placeholder="Type a message..." class="flex-1 border border-gray-300 rounded-full py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button class="ml-2 bg-blue-500 text-white rounded-full p-2 hover:bg-blue-600 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- User Dialog -->
<dialog id="userDialog" class="p-4 rounded-lg shadow-xl">
    <h2 id="dialogTitle" class="text-2xl font-bold mb-4"></h2>
    <p id="dialogContent" class="mb-4"></p>
    <button id="closeDialog" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition duration-150 ease-in-out">Close</button>
</dialog>
@endsection

@push('scripts')
<script>
    // Sample user data (existing chats)
    let currentChatUser = null;
    const userList = document.getElementById('userList');
    const newMessageBtn = document.getElementById('newMessageBtn');
    const userSearch = document.getElementById('userSearch');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    async function loadConversations(search = '') {
        try {
            const response = await fetch(`conversations?search=${encodeURIComponent(search)}`);
            const data  = await response.json();
            // console.log(data)
            populateConversationList(data.conversations);
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    }

    async function loadNewUsers(search = '') {
        try {
            const response = await fetch(`users?search=${encodeURIComponent(search)}`);
            const data = await response.json();
            // console.log(data)
            populateNewUserList(data.users);
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    function populateConversationList(conversations) {
        userList.innerHTML = '';
        conversations.forEach(convo => {
            const userDiv = document.createElement('div');
            userDiv.className = 'p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-150 ease-in-out';
            userDiv.innerHTML = `
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-300 rounded-full mr-4"></div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800">${convo.name}</h3>
                        <p class="text-sm text-gray-500 truncate">${convo.last_message}</p>
                    </div>
                </div>
            `;
            userDiv.addEventListener('click', () => openChat(convo.user));
            userList.appendChild(userDiv);
        });
    }

    function populateNewUserList(users) {
        userList.innerHTML = '';
        users.forEach(user => {
            const userDiv = document.createElement('div');
            userDiv.className = 'p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-150 ease-in-out bg-blue-50';
            userDiv.innerHTML = `
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-300 rounded-full mr-4"></div>
                    <div>
                        <h3 class="font-semibold text-gray-800">${user.name}</h3>
                            <p class="text-sm ${user.has_conversation ? 'text-gray-500' : 'text-blue-500'}">
                            ${user.has_conversation ? 'Existing Chat' : 'New Contact'}
                        </p>
                    </div>
                </div>
            `;
            userDiv.addEventListener('click', () => startNewChat(user));
            userList.appendChild(userDiv);
        });
    }

    // Event listeners
    newMessageBtn.addEventListener('click', async () => {
        if (newMessageBtn.textContent.includes('New Message')) {
            await loadNewUsers();
            newMessageBtn.textContent = 'Back to Chats';
        } else {
            await loadConversations();
            newMessageBtn.textContent = 'New Message';
        }
    });

    userSearch.addEventListener('input', async (e) => {
        const search = e.target.value;
        if (newMessageBtn.textContent.includes('Back to Chats')) {
        // console.log(search);

            await loadNewUsers(search);
        } else {
            await loadConversations(search);
        }
    });

    // Profile dropdown functionality
    const profileDropdown = document.getElementById('profileDropdown');
    const dropdownMenu = document.getElementById('dropdownMenu');

    profileDropdown.addEventListener('click', () => {
        dropdownMenu.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (event) => {
        if (!profileDropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });

    // Initial load
    loadConversations();
</script>
@endpush