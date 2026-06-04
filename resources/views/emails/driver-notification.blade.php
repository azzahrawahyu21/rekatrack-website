<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifikasi Rekatrack</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; padding:20px; }
        .container { max-width: 600px; margin:0 auto; background:white; padding:25px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1e40af; }
        .info { background:#f8fafc; padding:15px; border-radius:6px; margin:15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>{{ $notification->title }}</h2>
        
        <div class="info">
            <p><strong>{{ $notification->body }}</strong></p>
            
            @if($notification->travelDocument)
            <p>
                <strong>No. Surat Jalan :</strong> {{ $notification->travelDocument->no_travel_document }}<br>
                <strong>Project :</strong> {{ $notification->travelDocument->project }}
            </p>
            @endif
        </div>

        <small style="color:#64748b;">
            Email ini dikirim otomatis oleh Sistem Rekatrack<br>
            {{ now()->format('d M Y, H:i') }}
        </small>
    </div>
</body>
</html>