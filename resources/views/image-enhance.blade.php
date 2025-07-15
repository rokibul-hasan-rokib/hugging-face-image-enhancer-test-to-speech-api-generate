<form method="POST" enctype="multipart/form-data" action="{{ route('enhance') }}">
    @csrf
    <input type="file" name="image" accept="image/*" required>
    <button type="submit">Enhance</button>
</form>

@if(isset($enhanced))
    <h3>Enhanced Image:</h3>
    <img src="{{ $enhanced }}" style="max-width: 100%;">
@endif

