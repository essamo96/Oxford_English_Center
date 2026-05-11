@extends('emails.layout')

@section('content')
<div style="text-align: center;">
    <div style="margin-bottom: 25px;">
        <span style="display: inline-block; padding: 6px 14px; background-color: #ecfdf5; border-radius: 50px; font-size: 13px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 1px;">
            Registration Successful
        </span>
    </div>

    <h1>Welcome to Oxford, {{ $student->name }}!</h1>
    
    <p style="margin-top: 20px;">
        We are thrilled to have you join the Oxford English Centre community. Your registration has been successfully received and is now being processed by our team.
    </p>

    <!-- Details Box -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 15px; padding: 30px; margin: 35px 0; text-align: left;">
        <h2 style="font-size: 18px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">Your Details</h2>
        
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding: 10px 0; color: #64748b; font-weight: 600;">Full Name:</td>
                <td style="padding: 10px 0; color: #0f172a; text-align: right; font-weight: 700;">{{ $student->name }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #64748b; font-weight: 600;">Student ID:</td>
                <td style="padding: 10px 0; color: #0f172a; text-align: right; font-weight: 700;">#{{ 1000 + $student->id }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #64748b; font-weight: 600;">Program:</td>
                <td style="padding: 10px 0; color: #0f172a; text-align: right; font-weight: 700;">{{ ucfirst($student->program_type) }} Program</td>
            </tr>
        </table>
    </div>

    <p style="font-size: 15px; color: #64748b;">
        If you have applied for a placement test, our coordination team will contact you shortly to confirm your assessment appointment.
    </p>

    <div style="margin-top: 40px;">
        <a href="{{ url('/') }}" class="btn">Explore Courses</a>
    </div>
</div>
@endsection
