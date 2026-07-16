@extends('emails.layout')

@section('content')
<div style="text-align: center;">
    <div style="margin-bottom: 30px;">
        <span style="display: inline-block; padding: 8px 16px; background-color: #f1f5f9; border-radius: 50px; font-size: 17.2px; font-weight: 600; color: #002147;">
            ✨ Success Registration | تم التسجيل بنجاح
        </span>
    </div>

    <h1 style="color: #002147; font-size: 31.2px; margin-bottom: 20px;">Welcome to Oxford, {{ $student->name }}!</h1>
    <h2 style="color: #002147; font-size: 25.2px; margin-bottom: 30px; font-weight: 600;">أهلاً بك في مركز أكسفورد الإنجليزي</h2>

    <p style="color: #475569; font-size: 19.2px; margin-bottom: 25px;">
        We are thrilled to have you join our community! Your registration has been received and is currently being processed by our administrative team.
    </p>
    
    <p dir="rtl" style="color: #475569; font-size: 20.2px; margin-bottom: 40px; font-family: 'Amiri', serif;">
        نحن سعداء جداً بانضمامك إلينا! تم استلام طلب التسجيل الخاص بك بنجاح، ويقوم فريقنا الإداري حالياً بمراجعة البيانات.
    </p>

    <!-- Info Cards -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 40px; text-align: left;">
        <h3 style="color: #002147; font-size: 21.2px; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Registration Details | تفاصيل التسجيل</h3>
        
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Program:</td>
                <td style="padding: 8px 0; color: #0f172a; text-align: right; font-weight: 700;">{{ ucfirst($student->program_type) }} Program</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Student ID:</td>
                <td style="padding: 8px 0; color: #0f172a; text-align: right; font-weight: 700;">#{{ $student->id }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Join Date:</td>
                <td style="padding: 8px 0; color: #0f172a; text-align: right; font-weight: 700;">{{ date('M d, Y') }}</td>
            </tr>
        </table>
    </div>

    <p style="color: #475569; font-size: 18.2px; margin-bottom: 30px;">
        If you chose to take a placement test, one of our coordinators will contact you shortly to confirm the appointment.
    </p>

    <div style="margin: 40px 0;">
        <a href="{{ url('/') }}" style="background-color: #002147; color: #ffffff; padding: 16px 32px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 19.2px; box-shadow: 0 4px 6px rgba(0, 33, 71, 0.2);">
            Visit Our Website | زيارة موقعنا
        </a>
    </div>
</div>
@endsection
