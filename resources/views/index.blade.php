<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Test Fill Image API</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 2rem; }
    input, button { padding: 0.5rem; margin: 0.5rem 0; width: 100%; }
    img { max-width: 100%; margin-top: 1rem; }
    #result { margin-top: 1rem; }
  </style>
</head>
<body>

  <h2>Test Fill Image API</h2>

  <label for="background">Background Image URL:</label>
  <input type="text" id="background" placeholder="Enter image URL" value="https://raw.githubusercontent.com/gradio-app/gradio/main/test/test_files/bus.png" />

  <label for="prompt">Prompt:</label>
  <input type="text" id="prompt" placeholder="Enter prompt" value="RealVisXL V5.0 Lightning" />

  <button id="sendBtn">Generate Image</button>

  <div id="result"></div>

  <script>
    document.getElementById('sendBtn').addEventListener('click', async () => {
      const background = document.getElementById('background').value;
      const prompt = document.getElementById('prompt').value;
      const resultDiv = document.getElementById('result');
      resultDiv.innerHTML = 'Loading...';

      try {
        const response = await fetch('/api/fill-image', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ background, prompt }),
        });

        if (!response.ok) {
          const error = await response.text();
          resultDiv.innerHTML = `<p style="color:red;">Error: ${error}</p>`;
          return;
        }

        const data = await response.json();

        // Inspect data structure in console to adjust this part
        console.log('API response:', data);

        // Example: If the response contains a URL of generated image:
        // (Adjust this to your API's actual response format)
        if (data && data.length > 1 && data[1]) {
          const generatedImageUrl = data[1];  // This depends on API response

          resultDiv.innerHTML = `
            <h3>Generated Image:</h3>
            <img src="${generatedImageUrl}" alt="Generated" />
          `;
        } else {
          resultDiv.innerHTML = '<p>No image URL found in response.</p>';
        }
      } catch (err) {
        console.error(err);
        resultDiv.innerHTML = `<p style="color:red;">Error: ${err.message}</p>`;
      }
    });
  </script>
</body>
</html>
