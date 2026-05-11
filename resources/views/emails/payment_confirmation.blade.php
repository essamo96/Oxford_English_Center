@extends('emails.layout')

@section('content')
<div style="text-align: center;">
    <div style="margin-bottom: 30px;">
        <span style="display: inline-block; padding: 8px 16px; background-color: #ecfdf5; border-radius: 50px; font-size: 14px; font-weight: 600; color: #059669;">
            ✅ Payment Confirmed | تم تأكيد الدفع
        </span>
    </div>

    <h1 style="color: #002147; font-size: 28px; margin-bottom: 20px;">Payment Received!</h1>
    <h2 style="color: #002147; font-size: 22px; margin-bottom: 30px; font-weight: 600;">تم استلام رسوم اختبار المستوى بنجاح</h2>

    <p style="color: #475569; font-size: 16px; margin-bottom: 25px;">
        Dear {{ $student->name }}, we have successfully confirmed your payment for the English Placement Test.
    </p>
    
    <p dir="rtl" style="color: #475569; font-size: 17px; margin-bottom: 40px; font-family: 'Amiri', serif;">
        عزيزي {{ $student->name }}، تم تأكيد استلام رسوم اختبار تحديد المستوى بنجاح.
    </p>

    <!-- Info Cards -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 40px; text-align: left;">
        <h3 style="color: #002147; font-size: 18px; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Payment Details | تفاصيل الدفع</h3>
        
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Amount Paid:</td>
                <td style="padding: 8px 0; color: #059669; text-align: right; font-weight: 700;">{{ number_format($test->paid_amount, 2) }} ILS</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Payment Method:</td>
                <td style="padding: 8px 0; color: #0f172a; text-align: right; font-weight: 700;">{{ $test->payment_method->name ?? 'Direct Payment' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Student:</td>
                <td style="padding: 8px 0; color: #0f172a; text-align: right; font-weight: 700;">{{ $student->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Confirmation Date:</td>
                <td style="padding: 8px 0; color: #0f172a; text-align: right; font-weight: 700;">{{ date('M d, Y') }}</td>
            </tr>
        </table>
    </div>

    <p style="color: #475569; font-size: 15px; margin-bottom: 30px;">
        Our team will contact you to finalize the test appointment details.
    </p>

    <div style="margin: 40px 0;">
        <a href="{{ url('/') }}" style="background-color: #002147; color: #ffffff; padding: 16px 32px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 16px; box-shadow: 0 4px 6px rgba(0, 33, 71, 0.2);">
            Visit Our Website | زيارة موقعنا
        </a>
    </div>
</div>
@endsection
