<p><strong>Pozor:</strong> neexistuje nic jako Fuska platba nebo Fuska kurýr. Pozor na falešné stránky dopravců jako PPL, DPD, Zásilkovna nebo Česká pošta. Pokud Vás někdo kontaktuje a žádá přihlášení do bankovnictví, číslo karty nebo jiné citlivé údaje, jedná se o <strong>podvod</strong>.</p>

<p>Potvrzujeme přidání Vašeho inzerátu na Fuska.fun.</p>

<p><strong>Chcete svůj inzerát TOPovat (zvýhodnit)?</strong><br>
    Můžete zaplatit kartou nebo bankovním převodem (49 Kč), případně zaslat SMS ve tvaru <code>FUSKA</code> na číslo <strong>90709</strong> (cena 79 Kč). Obdržený kód poté vyplňte do políčka <em>poukázka</em>.</p>

<p>
    <strong>Odkaz na správu inzerátu:</strong><br>
    <a href="https://fuska.fun/jobs/{{ $job->slug }}">https://fuska.fun/manage/{{ $job->slug }}</a>
</p>

<hr>

<p><strong>{{ $job->title }}</strong></p>

<p>{!! nl2br(e($job->description)) !!}</p>

<p>
    <strong>Jméno:</strong> {{ $job->contact_name }}<br>
    <strong>Telefon:</strong> {{ $job->phone }}<br>
    <strong>E-mail:</strong> {{ $job->email }}<br>
    <strong>Cena:</strong> {{ $job->price_label ?? 'Dohodou' }}
</p>

<p>Vaše inzeráty najdete na:<br>
    <a href="https://fuska.fun/moje-inzeraty">https://fuska.fun/moje-inzeraty</a>
</p>

<p>S pozdravem,<br>tým <strong>Fuska.fun</strong></p>
