<form action="{{ route('tts.speak') }}" method="POST">
    @csrf
    <textarea name="text" rows="4" placeholder="Enter text..."></textarea>
    <button type="submit">Generate Speech</button>
</form>
