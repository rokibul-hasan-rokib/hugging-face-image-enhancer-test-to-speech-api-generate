<!DOCTYPE html>
<html>
<head>
    <title>Image Enhancer - CodeFormer</title>
</head>
<body>
    <h1>Upload Face Image</h1>
    <form action="{{ route('enhance.image') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" accept="image/*" required>
        <br><br>
        <button type="submit">Enhance</button>
    </form>
</body>
</html>
