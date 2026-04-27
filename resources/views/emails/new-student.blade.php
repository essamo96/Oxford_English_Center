@extends('emails.layout')

@section('content')
    <h2 style="color: #002147; font-size: 20px; margin-bottom: 20px; text-align: right;">أهلاً بك {{ $studentName }}،</h2>
    
    <p style="font-size: 16px; line-height: 1.6; color: #444; margin-bottom: 25px; text-align: right;">
        تم إنشاء حسابك بنجاح في <strong>{{ $mysettings->name ?? 'مركز أكسفورد' }}</strong>. يمكنك الآن تسجيل الدخول للوصول إلى دوراتك وتقاريرك.
    </p>

    <div style="background-color: #f8fafc; border-right: 4px solid #002147; padding: 25px; margin: 30px 0; border-radius: 4px;">
        <h3 style="color: #002147; margin-top: 0; font-size: 18px; text-align: right;">بيانات الدخول:</h3>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="text-align: right;">
            <tr>
                <td style="padding: 5px 0; color: #718096; width: 100px;">اسم المستخدم:</td>
                <td style="padding: 5px 0; color: #1a202c; font-weight: 700;">{{ $username }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0; color: #718096;">كلمة المرور:</td>
                <td style="padding: 5px 0; color: #1a202c; font-weight: 700;">{{ $password }}</td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; margin: 35px 0;">
        <a href="{{ $loginUrl }}" style="background-color: #002147; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            أكمل تسجيل الدخول / Finish Login
        </a>
    </div>

    <p style="font-size: 14px; color: #718096; text-align: right; line-height: 1.6;">
        يرجى الحرص على تغيير كلمة المرور بعد أول تسجيل دخول لضمان أمان حسابك.
    </p>
@stop
