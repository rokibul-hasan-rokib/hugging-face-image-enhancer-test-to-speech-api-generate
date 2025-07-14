<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text to Speech Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Hugging Face FastSpeech2 TTS Demo</h2>
                    </div>
                    <div class="card-body">
                        <form id="ttsForm">
                            @csrf
                            <div class="mb-3">
                                <label for="textInput" class="form-label">Enter Text:</label>
                                <textarea class="form-control" id="textInput" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Speech</button>
                        </form>
                        <div class="mt-4" id="audioContainer" style="display: none;">
                            <h4>Generated Audio:</h4>
                            <audio id="audioPlayer" controls class="w-100"></audio>
                            <div class="mt-2">
                                <a id="downloadLink" href="#" class="btn btn-success">Download Audio</a>
                            </div>
                        </div>
                        <div id="errorContainer" class="alert alert-danger mt-3" style="display: none;"></div>
                        <div id="loadingSpinner" class="text-center mt-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Generating audio... This may take a moment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#ttsForm').on('submit', function(e) {
                e.preventDefault();

                const text = $('#textInput').val().trim();
                if (!text) return;

                $('#loadingSpinner').show();
                $('#errorContainer').hide();
                $('#audioContainer').hide();

                $.ajax({
                    url: "{{ route('tts.generate') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        text: text
                    },
                    success: function(response) {
                        if (response.success) {
                            const audioSrc = `data:audio/${response.format};base64,${response.audio}`;
                            $('#audioPlayer').attr('src', audioSrc);
                            $('#downloadLink').attr('href', audioSrc)
                                .attr('download', `tts-output.${response.format}`);
                            $('#audioContainer').show();
                        } else {
                            showError('An error occurred during generation.');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showError(errorMsg);
                    },
                    complete: function() {
                        $('#loadingSpinner').hide();
                    }
                });
            });

            function showError(message) {
                $('#errorContainer').text(message).show();
            }
        });
    </script>
</body>
</html>