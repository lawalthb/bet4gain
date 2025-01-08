<!DOCTYPE html>
<html>
<head>
    <title>Reverb Test</title>
</head>
<body>
    <h1>Reverb WebSocket Test</h1>
    <div id="messages"></div>

    <script>
        // Initialize Reverb connection
        const reverb = new WebSocket(`ws://127.0.0.1:6001/app/12345678`);

        // Listen for connection open
        reverb.onopen = () => {
            console.log('Connected to Reverb');
            addMessage('Connected to WebSocket');

            // Subscribe to channel
            reverb.send(JSON.stringify({
                event: 'subscribe',
                channel: 'test-channel'
            }));
        };

        // Listen for messages
        reverb.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log('Received:', data);

            if (data.event === 'TestEvent') {
                addMessage('Received message: ' + data.message);
            }
        };

        // Handle connection errors
        reverb.onerror = (error) => {
            console.error('WebSocket error:', error);
            addMessage('Error: ' + error);
        };

        function addMessage(text) {
            const messages = document.getElementById('messages');
            messages.innerHTML += `<p>${text}</p>`;
        }
    </script>
</body>
</html>
