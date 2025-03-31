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
    let currentMessages = [];

    const userList = document.getElementById('userList');
    const newMessageBtn = document.getElementById('newMessageBtn');
    const userSearch = document.getElementById('userSearch');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    async function loadConversations(search = '') {
        try {
            const response = await fetch(`conversations?search=${encodeURIComponent(search)}`);
            const data  = await response.json();
            // console.log('conversations', data.conversations)
            // Convert object to array
            const conversations = Object.values(data.conversations);
            // console.log('Processed conversations:', conversations);
            
            populateConversationList(conversations);
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
            console.log(convo);
            const userDiv = document.createElement('div');
            userDiv.className = 'p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-150 ease-in-out';
            userDiv.innerHTML = `
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-300 rounded-full mr-4"></div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-gray-800">${convo.name}</h3>
                            ${convo.unread_count > 0 ? `
                                <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    ${convo.unread_count}
                                </span>
                            ` : ''}
                        </div>
                        <p class="text-sm text-gray-500 truncate">${convo.last_message}</p>
                    </div>
                </div>
            `;
            userDiv.addEventListener('click', () => openChat(convo.user_id));
            userList.appendChild(userDiv);
        });
    }

    function populateNewUserList(users) {
        userList.innerHTML = '';
        users.forEach(user => {
            // console.log(user)
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

    async function openChat(userId) {
        try {
            // Fetch user details
            const userResponse = await fetch(`users/${userId}`);
            const user = await userResponse.json();

            // console.log(user);
            
            // Update current chat user
            currentChatUser = user;
            chatInput.classList.remove('hidden');
            
            // Update chat header
            document.getElementById('chatHeader').innerHTML = `
                <div class="w-10 h-10 bg-gray-300 rounded-full mr-3"></div>
                <h2 class="text-xl font-semibold text-gray-800">${user.first_name} ${user.last_name}</h2>
            `;

            // Fetch messages
            const messagesResponse = await fetch(`messages/${userId}`);
            const messages = await messagesResponse.json();
            console.log(messages.messages.data);
            currentMessages = messages.messages.data; // Store messages in array
            renderMessages(currentMessages);

        } catch (error) {
            console.error('Error loading chat:', error);
            // Handle error (show error message to user)
        }

        // Mobile view handling
        if (window.innerWidth < 640) {
            document.getElementById('sidebar').classList.add('hidden');
            document.getElementById('chatArea').classList.remove('hidden');
        }
    }

    function renderMessages(messages) {
        chatMessages.innerHTML = '';
        messages.forEach(message => {
            const isSender = message.sender_id === {{ auth()->id() }};
            const timeString = formatMessageTime(message.created_at);
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${isSender ? 'justify-end' : 'justify-start'} mb-2`;
            
            messageDiv.innerHTML = `
                <div class="max-w-[70%] ${isSender ? 'bg-blue-500' : 'bg-gray-200'} rounded-xl p-2 px-3 relative">
                    <p class="text-[15px] ${isSender ? 'text-white' : 'text-gray-800'}">${message.message}</p>
                    <div class="flex items-center justify-end gap-1 mt-1">
                        <span class="text-xs ${isSender ? 'text-blue-100' : 'text-gray-500'}">${timeString}</span>
                        ${isSender ? `
                            <span class="text-[10px] ${isSender ? 'text-blue-200' : 'text-gray-400'}">
                                ${getStatusIcon(message.status)}
                            </span>
                        ` : ''}
                    </div>
                </div>
            `;
            chatMessages.appendChild(messageDiv);
        });
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Helper functions
    function formatMessageTime(timestamp) {
        const options = { hour: 'numeric', minute: '2-digit', hour12: true };
        return new Date(timestamp).toLocaleTimeString('en-US', options).replace(/:\d+ /, ' ');
    }

    function getStatusIcon(status) {
        const icons = {
            sent: '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>',
            delivered: `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                <path d="M18 7l-1.4-1.4L9 16.2l-4.2-4.2-1.4 1.4L9 19 18 7z"/>
            </svg>`,
            read: `<svg class="w-3 h-3 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
            </svg>`
        };
        return icons[status] || icons.sent;
    }

    async function startNewChat(user) {
        await openChat(user.id);
        loadConversations();
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

    // Message sending
    document.querySelector('#chatInput button').addEventListener('click', async () => {
        const input = document.querySelector('#chatInput input');
        const message = input.value.trim();
        
        if (message && currentChatUser) {
            try {
                const response = await fetch(`messages/${currentChatUser.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                });
                
                if (!response.ok) throw new Error('Failed to send message');
                
                const newMessage = await response.json();
                input.value = '';
                
                // Add to current messages and re-render
                currentMessages.push(newMessage.message);
                renderMessages(currentMessages);
                
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Error sending message: ' + error.message);
                input.value = message; // Restore message
            }
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