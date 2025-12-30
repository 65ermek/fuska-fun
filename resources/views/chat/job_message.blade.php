{{-- resources/views/emails/job_message.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nová odpověď na inzerát: {{ $job->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .message { background: white; padding: 15px; border-left: 4px solid #3b82f6; margin: 15px 0; }
        .button { display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Nová zpráva k inzerátu</h1>
    </div>

    <div class="content">
        <h2>{{ $job->title }}</h2>

        <p><strong>Od:</strong> {{ $fromEmail }}</p>
        <p><strong>Zpráva:</strong></p>

        <div class="message">
            {{ $text }}
        </div>

        @if(!empty($chat_link))
            <div style="margin: 20px 0; padding: 15px; background: #e8f4fd; border: 1px solid #b3d9ff;">
                <p><strong>💬 Nový chat:</strong> Můžete pokračovat v konverzaci přímo v chatu:</p>
                <a href="{{ $chat_link }}" class="button">
                    Otevřít chat
                </a>
            </div>
        @endif

        <p style="font-size: 14px; color: #6b7280; margin-top: 20px;">
            Odpověď na tento email není možná. Použijte prosím výše uvedený odkaz na chat.
        </p>
    </div>

    <div class="footer">
        <p>Toto je automatické oznámení. Prosím, neodpovídejte na tento email.</p>
    </div>
</div>
</body>
</html>
