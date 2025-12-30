{{-- resources/views/emails/chat_notification.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nová zpráva v chatu - {{ $job->title }}</title>
</head>
<body>
<h2>Nová zpráva v chatu</h2>

<p><strong>Inzerát:</strong> {{ $job->title }}</p>
<p><strong>Od:</strong> {{ $fromEmail }}</p>
<p><strong>Zpráva:</strong></p>
<div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #3b82f6; margin: 15px 0;">
    {{ $text }}
</div>

<p>
    <a href="{{ $chat_link }}" style="display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px;">
        Odpovědět v chatu
    </a>
</p>

<hr>
<p style="color: #6b7280; font-size: 12px;">
    Toto je automatické oznámení. Prosím, neodpovídejte na tento e-mail.
</p>
</body>
</html>
