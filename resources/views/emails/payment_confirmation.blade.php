@extends('emails.layout')

@section('content')
<div style="text-align: center;">
    <div style="margin-bottom: 25px;">
        <span style="display: inline-block; padding: 6px 14px; background-color: #ecfdf5; border-radius: 50px; font-size: 13px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 1px;">
            Payment Received
        </span>
    </div>

    <h1>Thank you for your payment!</h1>
    
    <p style="margin-top: 20px;">
        Dear {{ $test->student->name }}, your payment for the placement test has been confirmed. Below is a summary of your transaction.
    </p>

    <!-- Invoice Card -->
    <div style="background-color: #ffffff; border: 2px solid #f1f5f9; border-radius: 15px; margin: 35px 0; overflow: hidden;">
        <div style="background-color: #f8fafc; padding: 20px; border-bottom: 1px solid #f1f5f9; text-align: left;">
            <table width="100%">
                <tr>
                    <td>
                        <span style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Receipt No.</span><br>
                        <span style="color: #002147; font-size: 16px; font-weight: 700;">#INV-{{ date('Y') }}-{{ $test->id }}</span>
                    </td>
                    <td align="right">
                        <span style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Date</span><br>
                        <span style="color: #002147; font-size: 16px; font-weight: 700;">{{ date('M d, Y') }}</span>
                    </td>
                </tr>
            </table>
        </div>
        
        <div style="padding: 30px; text-align: left;">
            <table width="100%" style="border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9;">
                        <th align="left" style="padding-bottom: 15px; color: #64748b; font-size: 13px;">Description</th>
                        <th align="right" style="padding-bottom: 15px; color: #64748b; font-size: 13px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 20px 0; color: #1e293b; font-weight: 600;">Placement Test Fee</td>
                        <td align="right" style="padding: 20px 0; color: #1e293b; font-weight: 700;">{{ $test->paid_amount }} ILS</td>
                    </tr>
                    <tr style="border-top: 2px solid #f1f5f9;">
                        <td style="padding-top: 20px; color: #002147; font-weight: 800; font-size: 18px;">Total Paid</td>
                        <td align="right" style="padding-top: 20px; color: #002147; font-weight: 800; font-size: 18px;">{{ $test->paid_amount }} ILS</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #f1f5f9;">
            <span style="color: #64748b; font-size: 13px;">Payment Method: <strong>{{ $test->paymentMethod->name ?? 'Direct Payment' }}</strong></span>
        </div>
    </div>

    <p style="font-size: 14px; color: #94a3b8; font-style: italic;">
        Note: This is a system-generated receipt and does not require a signature.
    </p>

    <div style="margin-top: 40px;">
        <a href="{{ url('/') }}" class="btn">Return to Student Portal</a>
    </div>
</div>
@endsection
