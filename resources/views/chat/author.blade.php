<!DOCTYPE html>
<html>
<head>
    <title>Chat s kandidátem</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .chat-container { max-width: 800px; margin: 0 auto; }
        .chat-header { background: #007bff; color: white; padding: 15px; border-radius: 10px 10px 0 0; }
        .chat-messages { height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; background: #f8f9fa; }
        .message { margin-bottom: 10px; padding: 8px 12px; border-radius: 15px; max-width: 80%; }
        .message-outgoing { background: #dcf8c6; margin-left: auto; text-align: right; }
        .message-incoming { background: white; border: 1px solid #e0e0e0; }
        .chat-input { display: flex; padding: 10px; border-top: 1px solid #ddd; background: white; }
        .chat-input input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px; margin-right: 10px; }
        .debug-info { background: #fff3cd; padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 12px; }
    </style>
</head>
<body>
<div class="chat-container">
    <!-- Отладочная информация -->
    <div class="debug-info">
        <strong>DEBUG INFO:</strong><br>
        Token: {{ $token }}<br>
        Job ID: {{ $job->id }}<br>
        Candidate: {{ $candidateEmail }}<br>
        Messages count: {{ $chatHistory->count() }}<br>
        @foreach($chatHistory as $index => $message)
            Message {{ $index + 1 }}: {{ $message->name }} - {{ Str::limit($message->message, 50) }}<br>
        @endforeach
    </div>

    <div class="chat-header">
        <h1>💬 Chat s kandidátem</h1>
        <p><strong>Inzerát:</strong> {{ $job->title }} | <strong>Kandidát:</strong> {{ $candidateEmail }}</p>
    </div>

    <div class="chat-messages" id="chatMessages">
        @foreach($chatHistory as $message)
            <div class="message {{ $message->name === 'candidate' ? 'message-incoming' : 'message-outgoing' }}">
                <strong>{{ $message->name === 'candidate' ? 'Kandidát' : 'Vy' }}:</strong>
                {{ $message->message }}
                <div style="font-size: 0.8em; color: #666;">
                    {{ $message->created_at->format('H:i') }}
                </div>
            </div>
        @endforeach

        @if($chatHistory->count() === 0)
            <div style="text-align: center; color: #666; padding: 20px;">
                Žádné zprávy v chatu. Pošlete první zprávu!
            </div>
        @endif
    </div>

    <form action="{{ route('chat.author.send') }}" method="POST" class="chat-input">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="job_id" value="{{ $job->id }}">

        <input type="text" name="message" placeholder="Napište zprávu..." required>
        <button type="submit" style="background: #ffc107; color: black; padding: 10px 20px; border: none; border-radius: 20px; cursor: pointer;">
            Odeslat
        </button>
    </form>
</div>

<script>
    // Автоматическая прокрутка вниз
    const messagesDiv = document.getElementById('chatMessages');
    if (messagesDiv) {
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    console.log('Chat initialized:', {
        token: '{{ $token }}',
        jobId: {{ $job->id }},
        candidate: '{{ $candidateEmail }}',
        messageCount: {{ $chatHistory->count() }}
    });
</script>
</body>
</html>
