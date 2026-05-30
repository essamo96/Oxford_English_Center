<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تأكيد الدفعة</title>
<style>
  body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; background: #f7f8fb; margin: 0; padding: 24px; color: #1f2937; }
  .wrap { max-width: 620px; margin: 0 auto; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,51,102,0.08); }
  .hdr { background: linear-gradient(135deg, #003366, #002a55); padding: 28px 28px 22px; text-align: center; color: #fff; }
  .hdr h1 { margin: 0; font-size: 22px; font-weight: 700; }
  .hdr p { margin: 6px 0 0; opacity: 0.85; font-size: 14px; }
  .body { padding: 28px; line-height: 1.8; font-size: 15px; }
  .amount-card { background: #fff9e0; border-right: 4px solid #ffcc00; padding: 16px 18px; border-radius: 10px; margin: 18px 0; }
  .amount-card .lbl { font-size: 12px; font-weight: 700; color: #6c7689; text-transform: uppercase; letter-spacing: 0.08em; }
  .amount-card .val { font-size: 22px; font-weight: 800; color: #003366; }
  .ftr { background: #fafbfd; text-align: center; padding: 18px; color: #6c7689; font-size: 12px; border-top: 1px solid #e5e9f0; }
  .badge-ok { display: inline-block; background: #d1fae5; color: #065f46; font-weight: 700; padding: 4px 12px; border-radius: 999px; font-size: 12px; }
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>✅ تم تأكيد دفعتك</h1>
    <p>Payment Confirmation · Oxford English Center</p>
  </div>
  <div class="body">
    <p>عزيزي/عزيزتي <strong>{{ $student->name }}</strong>،</p>
    <p>يسعدنا إبلاغك بأنه تم تأكيد دفعتك المالية بنجاح من قِبل إدارة الأكاديمية.</p>

    @if(!empty($programTitle) || !empty($groupName))
    <div style="background:#eef5ff;border-right:4px solid #003366;padding:14px 18px;border-radius:10px;margin:18px 0;">
      <div style="font-size:12px;font-weight:700;color:#6c7689;letter-spacing:.06em;">البرنامج الدراسي</div>
      <div style="font-size:18px;font-weight:800;color:#003366;">{{ $programTitle ?? '—' }}</div>
      @if(!empty($groupName))
        <div style="font-size:12px;font-weight:700;color:#6c7689;margin-top:8px;">المجموعة / المستوى</div>
        <div style="font-size:15px;font-weight:700;color:#0b5394;">{{ $groupName }}</div>
      @endif
    </div>
    @endif

    @if($fee)
    <div class="amount-card">
      <div class="lbl">المبلغ المؤكَّد</div>
      <div class="val">{{ number_format($fee->transaction_amount ?? $fee->student_fee_paid, 2) }} ILS</div>
      @if($fee->remaining_amount > 0)
        <div class="lbl mt-2" style="margin-top:8px;">المتبقي على البرنامج</div>
        <div style="font-size:16px;font-weight:700;color:#dc3545;">{{ number_format($fee->remaining_amount, 2) }} ILS</div>
      @else
        <div style="margin-top:8px;"><span class="badge-ok">مكتمل السداد</span></div>
      @endif
    </div>
    @endif

    <p>يمكنك تسجيل الدخول إلى حسابك ومتابعة برنامجك من خلال موقعنا الرسمي.</p>
    <p>للاستفسارات يمكنك التواصل معنا في أي وقت.</p>
    <p style="margin-top:24px;">مع التحية،<br><strong>إدارة Oxford English Center</strong></p>
  </div>
  <div class="ftr">© {{ date('Y') }} Oxford English Center — جميع الحقوق محفوظة</div>
</div>
</body>
</html>
