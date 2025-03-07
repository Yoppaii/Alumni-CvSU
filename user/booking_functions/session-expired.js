function checkSessionStatus() {
    fetch('/Alumni-CvSU/user/session-check-endpoint.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.sessionExpired) {
                alert('Session expired. Please log in again.');
                window.location.href = '/Alumni-CvSU/portal/login';
            } else if (data.error) {
                console.error('Server error:', data.error);
            }
        })
        .catch(error => {
            console.error('Error checking session:', error);
            if (error instanceof SyntaxError) {
                console.error('Response is not valid JSON');
            }
        });
}

setInterval(checkSessionStatus, 60000);