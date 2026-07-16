@extends('emails.layout')

@section('content')
<div style="text-align: center;">
    <div style="margin-bottom: 30px;">
        <span style="display: inline-block; padding: 8px 16px; background-color: #e0f2fe; border-radius: 50px; font-size: 17.2px; font-weight: 600; color: #0369a1;">
            📅 Appointment Confirmation | تأكيد موعد
        </span>
    </div>

    <h1 style="color: #002147; font-size: 27.2px; margin-bottom: 20px;">Dear {{ $test->student->name }},</h1>

    <div style="color: #475569; font-size: 19.2px; margin-bottom: 30px; line-height: 1.8; text-align: left;">
        {!! nl2br(e($customMessage)) !!}
    </div>

    <!-- Appointment Box -->
    <div style="background-color: #f0f9ff; border: 2px solid #bae6fd; border-radius: 16px; padding: 30px; margin-bottom: 40px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" style="padding-bottom: 15px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2838/2838779.png" width="50" alt="Calendar">
                </td>
            </tr>
            <tr>
                <td align="center">
                    <div style="font-size: 23.2px; font-weight: 800; color: #002147; margin-bottom: 5px;">
                        {{ \Carbon\Carbon::parse($test->test_date)->format('l, F j, Y') }}
                    </div>
                    <div style="font-size: 21.2px; font-weight: 600; color: #0369a1;">
                        at {{ $test->test_time }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <p style="color: #64748b; font-size: 17.2px; font-style: italic; margin-bottom: 30px;">
        Please arrive at the center 15 minutes before your scheduled time.
        <br>يرجى الحضور إلى المركز قبل 15 دقيقة من الموعد المحدد.
    </p>

    <div style="margin: 40px 0;">
        <a href="https://maps.google.com/?q={{ urlencode($mysettings->address ?? 'Oxford English Centre') }}" style="background-color: #0369a1; color: #ffffff; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 18.2px;">
            Get Directions | موقع المركز
        </a>
    </div>
</div>
@endsection
