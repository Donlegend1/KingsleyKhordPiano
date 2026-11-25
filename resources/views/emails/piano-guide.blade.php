@extends('layouts.email')

@section('content')
    <h2 style="color:#151a22;">Your piano guide is ready</h2>
    <p style="color:#4f5667;line-height:1.6;">Hi,</p>
    <p style="color:#4f5667;line-height:1.6;">Here’s your free <strong>{{ $guideTitle }}</strong> PDF. Open it or save a copy to keep beside you at the piano.</p>
    <p style="margin:24px 0;">
        <a href="{{ $pdfUrl }}" style="display:inline-block;padding:12px 20px;border-radius:8px;background:#ffd736;color:#151a22;font-weight:bold;text-decoration:none;">Open your PDF</a>
    </p>
    @if($practiceTip)
        <p style="padding:14px 16px;border-left:3px solid #ffd736;background:#f9f7f0;color:#151a22;line-height:1.6;">{{ $practiceTip }}</p>
    @endif
    <p style="color:#4f5667;font-size:14px;">Save a copy within 7 days if you’d like to keep it.</p>
    <p style="color:#4f5667;">Happy practising,<br>Kingsley</p>
@endsection
