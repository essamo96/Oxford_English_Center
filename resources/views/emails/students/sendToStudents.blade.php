@extends('emails.layout')

@section('content')
    <div style="margin-bottom: 20px;">
        <h2 style="color: #002147; font-size: 23.2px; font-weight: 700; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; text-align: center;">
            {{ $title }}
        </h2>
        
        @if(isset($name))
            <p style="font-weight: 600; color: #1e293b; font-size: 19.2px; margin-bottom: 20px;">
                Dear {{ $name }},
            </p>
        @endif
        
        <div style="background-color: #ffffff; color: #334155; line-height: 1.8; font-size: 18.2px;">
            {!! nl2br($mailContent) !!}
        </div>
    </div>

    {{-- Professional Attachment Section --}}
    @if(isset($attachment) && $attachment)
        <div style="margin-top: 35px; padding-top: 25px; border-top: 2px dashed #e2e8f0;">
            <p style="font-size: 17.2px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">
                <i class="bi bi-paperclip"></i> Attached Document
            </p>
            
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s;">
                <tr>
                    <td style="padding: 20px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="48" style="vertical-align: middle;">
                                    <div style="background-color: #002147; border-radius: 8px; width: 48px; height: 48px; text-align: center; line-height: 48px;">
                                        <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" alt="File" width="24" style="vertical-align: middle;">
                                    </div>
                                </td>
                                <td style="padding-left: 20px; vertical-align: middle;">
                                    <div style="font-size: 18.2px; font-weight: 600; color: #1e293b;">Click to View or Download</div>
                                    <div style="font-size: 16.2px; color: #94a3b8;">Document securely attached to this email</div>
                                </td>
                                <td align="right" style="vertical-align: middle;">
                                    <a href="{{ $attachment }}" target="_blank" style="background-color: #002147; color: #ffffff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 17.2px; display: inline-block;">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div style="margin-top: 40px; text-align: center;">
        <p style="font-size: 16.2px; color: #94a3b8;">
            This is an automated notification from {{ $mysettings->name ?? 'Oxford English Centre' }}.
        </p>
    </div>
@endsection
