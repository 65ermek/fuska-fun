{{-- resources/views/emails/job_message.blade.php --}}
<p>
    Fuska.fun - odpověď na inzerát {{ $job->id }} - {{ $job->title }}
</p>

<hr style="margin: 15px 0; border: none; border-top: 1px solid #eee;">

<p>
    {{ $text }}
</p>

<p>
    <strong>{{ $name ?? 'Kandidát' }}</strong><br>
    {{ $fromEmail }}<br>
    @if(!empty($phone)){{ $phone }}<br>@endif
</p>

<hr style="margin: 15px 0; border: none; border-top: 1px solid #eee;">

@if(!empty($chat_link))
    <p style="text-align: center; margin: 20px 0;">
        <a href="{{ $chat_link }}" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;">
            💬 Otevřít chat
        </a>
    </p>
@endif

<p style="font-size: 12px; color: #666; margin-top: 25px;">
    <strong>Inzerát {{ $job->id }}:</strong> {{ $job->title }}<br>
    Cena: {{ $job->price ?? 'Dohodou' }}<br>
    <a href="{{ route('jobs.show', $job->slug) }}">https://fuska.fun/jobs/{{ $job->slug }}</a>
</p>

<p style="font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 10px; margin-top: 20px;">
    Email byl odeslán ze serveru Fuska.fun.<br>
    Tento email je určen autorovi inzerátu. Kliknutím na tlačítko se přihlásíte do chatu.
</p>
