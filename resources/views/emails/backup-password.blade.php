@php
    $emailTitle = 'Database Backup Password';
    $eyebrow = 'Administrator Security Notice';
    $accent = '#334155';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello Administrator,</p>
    <p style="margin:0 0 16px;">A database backup was created. Store the password below separately from the backup file.</p>
    <div style="margin:24px 0; padding:20px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; color:#0f172a; font-family:'Courier New', monospace; font-size:24px; font-weight:700; text-align:center; word-break:break-all;">
        {{ $password }}
    </div>
    <div style="margin:22px 0; padding:16px 18px; background:#f8fafc; border-left:4px solid {{ $accent }}; border-radius:8px;">
        <strong>File:</strong> {{ $fileName }}<br>
        <strong>Performed by:</strong> {{ $performedBy }}<br>
        <strong>Created:</strong> {{ $timestamp }}
    </div>
    <p style="margin:0;"><strong>Security reminder:</strong> Do not share this password with unauthorized persons. Contact the system administrator immediately if you did not initiate this backup.</p>
@endsection
