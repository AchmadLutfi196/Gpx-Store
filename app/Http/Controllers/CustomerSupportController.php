<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CustomerSupportController extends Controller
{
    /**
     * Display the customer support page with message history
     */
    public function index()
    {
        // Get authenticated user
        $user = Auth::user();
        
        // Group messages by conversation
        $conversations = ContactMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('conversation_id');
        
        return view('customer-support.index', compact('user', 'conversations'));
    }
    
    /**
     * View a specific conversation
     */
    public function viewConversation($conversationId)
    {
        $user = Auth::user();
        
        // Get all messages in this conversation
        $messages = ContactMessage::conversation($conversationId)
            ->where('user_id', $user->id)
            ->orWhere(function($query) use ($conversationId) {
                $query->where('conversation_id', $conversationId)
                      ->whereNotNull('admin_response');
            })
            ->get();
        
        // Mark unread messages as read
        ContactMessage::conversation($conversationId)
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return view('customer-support.conversation', compact('user', 'messages', 'conversationId'));
    }
    
    /**
     * Create a new support message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        $user = Auth::user();
        
        // Generate a new conversation ID if not provided
        $conversationId = $request->conversation_id ?? Str::uuid();
        
        // Create the message
        ContactMessage::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'conversation_id' => $conversationId,
            'is_read' => false,
            'response_sent' => false,
        ]);
        
        return redirect()
            ->route('customer-support.conversation', $conversationId)
            ->with('success', 'Your message has been sent successfully. Our team will respond shortly.');
    }
    
    /**
     * Reply to an existing conversation
     */
    public function reply(Request $request, $conversationId)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
        
        $user = Auth::user();
        
        // Get the original conversation subject
        $originalMessage = ContactMessage::where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->first();
            
        if (!$originalMessage) {
            return redirect()->back()->with('error', 'Conversation not found.');
        }
        
        // Create the reply message
        ContactMessage::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'subject' => 'RE: ' . $originalMessage->subject,
            'message' => $validated['message'],
            'conversation_id' => $conversationId,
            'is_read' => false,
            'response_sent' => false,
        ]);
        
        return redirect()
            ->route('customer-support.conversation', $conversationId)
            ->with('success', 'Your reply has been sent.');
    }
}
