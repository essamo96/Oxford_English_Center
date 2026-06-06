@extends('emails.layout')

@section('content')
    <h1 style="margin-top:0;">Hello {{ $contactName }},</h1>

    @if(!empty($originalSubject))
        <p style="margin-top:18px;">Thank you for reaching out to us. Below is our reply regarding your message <strong>&ldquo;{{ $originalSubject }}&rdquo;</strong>:</p>
    @else
        <p style="margin-top:18px;">Thank you for reaching out to us. Below is our reply to your message:</p>
    @endif

    <div style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:24px 26px;margin:24px 0;">
        <p style="margin:0;font-size:16px;line-height:1.7;color:#1e293b;white-space:pre-line;">{{ $replyBody }}</p>
    </div>

    @if(!empty($originalMessage))
        <p style="font-size:13px;color:#94a3b8;margin-top:30px;border-top:1px solid #f1f5f9;padding-top:20px;">
            <strong style="color:#64748b;">Your original message:</strong><br>
            <span style="white-space:pre-line;">{{ $originalMessage }}</span>
        </p>
    @endif
@stop
