<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function getConversations(Request $request)
    {
        try {
            $userId = Auth::id();
            $search = $request->input('search');

            // Get conversations with latest messages for each user
            $conversations = Message::with(['sender', 'receiver'])
                ->where(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)
                        ->orWhere('receiver_id', $userId);
                })
                ->latest()
                ->get()
                ->unique(function ($message) use ($userId) {
                    return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
                })
                ->map(function ($message) use ($userId) {
                    $partner = $message->sender_id === $userId ? $message->receiver : $message->sender;

                    return [
                        'user_id'       => $partner->id,
                        'name'          => trim($partner->first_name . ' ' . $partner->last_name),
                        'profile_photo' => $partner->profile_photo,
                        'last_message'  => $message->message,
                        'unread_count'  => Message::where('sender_id', $partner->id)
                            ->where('receiver_id', $userId)
                            ->whereNull('read_at')
                            ->count(),
                        'timestamp'     => $message->created_at->diffForHumans(),
                    ];
                });

            // Apply search filter if a search query exists
            if ($search) {
                $conversations = $conversations->filter(function ($conversation) use ($search) {
                    return stripos($conversation['name'], $search) !== false;
                })->values(); // Reset array keys after filtering
            }

            return response()->json([
                'conversations' => $conversations,
                'status'        => 'success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading conversations',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function searchUsers(Request $request)
    {
        try {
            $search = $request->input('search');

            $users = User::where('id', '!=', Auth::id())
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhere('username', 'like', "%$search%");
                    });
                })
                ->select(['id', 'username', 'first_name', 'last_name', 'email', 'profile_photo'])
                ->limit(20)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'has_conversation' => Message::where(function ($query) use ($user) {
                            $query->where('sender_id', Auth::id())
                                ->where('receiver_id', $user->id);
                        })
                        ->orWhere(function ($query) use ($user) {
                            $query->where('sender_id', $user->id)
                                ->where('receiver_id', Auth::id());
                        })
                        ->exists()
                    ];
                });

            return response()->json([
                'users' => $users,
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error searching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    // Get messages for specific conversation
    public function getMessages(User $user)
    {
        try {
            // Mark messages as read when opening conversation
            Message::where('sender_id', $user->id)
                ->where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = Message::with('sender')
                ->where(function($query) use ($user) {
                    $query->where('sender_id', Auth::id())
                        ->where('receiver_id', $user->id);
                })
                ->orWhere(function($query) use ($user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', Auth::id());
                })
                ->orderBy('created_at', 'asc')
                ->paginate(20);

            return response()->json([
                'messages' => $messages,
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Send new message
    public function sendMessage(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 'error'
            ], 422);
        }

        try {
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $user->id,
                'message' => $request->message
            ]);

            // Load relationship for response
            $message->load('sender');

            return response()->json([
                'message' => $message,
                'status' => 'success'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error sending message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Search users for new conversations
    public function _searchUsers(Request $request)
    {
        \Log::info($request->all());
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => 'error'
                ], 422);
            }

            $users = User::where('id', '!=', Auth::id())
                ->when($request->query('query'), function($query, $search) {
                    $query->where(function($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                          ->orWhere('last_name', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%");
                    });
                })
                ->select(['id', 'first_name', 'last_name', 'email', 'profile_photo'])
                ->limit(20)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'avatar' => $user->avatar_url,
                        'email' => $user->email,
                        'has_conversation' => $this->hasExistingConversation($user->id)
                    ];
                });

            return response()->json([
                'users' => $users,
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error searching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Check if conversation exists with user
    private function hasExistingConversation($userId)
    {
        return Message::where(function($query) use ($userId) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', Auth::id());
            })
            ->exists();
    }
}
