@php
    $emailTitle = 'Password Changed Successfully';
    $eyebrow = 'Security Notice';
    $accent = '#15803d';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $studentName }}</strong>,</p>
    <p style="margin:0 0 16px;">This confirms that your account password was changed successfully.</p>
    <div style="margin:22px 0; padding:16px 18px; background:#f0fdf4; border-left:4px solid {{ $accent }}; border-radius:8px;">
        <strong>Changed:</strong> {{ $changedAt }}<br>
        <strong>Account:</strong> {{ $studentName }}
    </div>
    <p style="margin:0;"><strong>Did not make this change?</strong> Contact the registrar's office immediately so your account can be secured.</p>
@endsection
