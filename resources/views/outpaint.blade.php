<!DOCTYPE html>
<html>
<head>
    <title>Laravel Outpaint</title>
</head>
<body>
    <h1>AI Image Outpaint</h1>
    @if(session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif

    {{-- <form action="{{ route('outpaint.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" required>
        <button type="submit">Upload & Outpaint</button>
    </form> --}}
    <form method="POST" action="{{ route('outpaint.process') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" required>
        <button type="submit">Enhance Image</button>
    </form>
</body>
</html>
