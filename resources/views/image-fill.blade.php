<!DOCTYPE html>
<html>
<head>
    <title>Image Fill Test</title>
</head>
<body>
    <h1>Test Clear Result</h1>
    <a href="/clear-result">Clear Result</a>

    <h1>Test Fill Image</h1>
    <form action="/fill-image" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label>Upload Image:</label>
            <input type="file" name="image" accept="image/*" required>
        </div>
        <div>
            <label>Model:</label>
            <input type="text" name="model" required>
        </div>
        <button type="submit">Submit</button>
    </form>
</body>
</html>