@extends('emails.layout')

@section('content')
<div style="text-align: center;">
    <div style="margin-bottom: 25px;">
        <span style="display: inline-block; padding: 6px 14px; background-color: #eff6ff; border-radius: 50px; font-size: 13px; font-weight: 700; color: #1d4ed8; text-transform: uppercase; letter-spacing: 1px;">
            Appointment Confirmed
        </span>
    </div>

    <h1>Assessment Scheduled</h1>
    
    <p style="margin-top: 20px; text-align: left;">
        Dear {{ $test->student->name }},<br><br>
        {!! nl2br(e($customMessage)) !!}
    </p>

    <!-- Appointment Box -->
    <div style="background: linear-gradient(135deg, #002147 0%, #003d7a 100%); border-radius: 15px; padding: 35px; margin: 35px 0; color: #ffffff;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" style="padding-bottom: 20px;">
                    <div style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.1); border-radius: 50%; line-height: 60px; text-align: center;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2838/2838779.png" width="30" style="vertical-align: middle; filter: brightness(0) invert(1);">
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <div style="font-size: 22px; font-weight: 800; margin-bottom: 8px;">
                        {{ \Carbon\Carbon::parse($test->test_date)->format('l, F j, Y') }}
                    </div>
                    <div style="font-size: 18px; font-weight: 400; opacity: 0.9;">
                        at {{ $test->test_time }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 20px; margin-bottom: 30px; text-align: left;">
        <p style="color: #92400e; font-size: 14px; margin: 0; line-height: 1.5;">
            <strong>Pro-tip:</strong> Please arrive at the center at least 15 minutes before your scheduled time for registration. Don't forget to bring your ID.
        </p>
    </div>

    <div style="margin-top: 40px;">
        <a href="https://maps.google.com/?q={{ urlencode($mysettings->address ?? 'Oxford English Centre') }}" class="btn">View Center Location</a>
    </div>
</div>
@endsection
