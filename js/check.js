document.addEventListener('DOMContentLoaded', () => {
    var ticketType = localStorage.getItem('ticketType');
    var eventName = localStorage.getItem('eventName');
    const checkoutButton = document.getElementById('submit-btn');
    const logo = document.getElementById('logo');

    logo.addEventListener('click', function () {
        window.location.href = 'index.html';
    });

    checkoutButton.addEventListener('click', function () {
        var userName = document.getElementById('userName').value;
        var userEmail = document.getElementById('userEmail').value;

        if (userName.trim() === "" || userEmail.trim() === "") {
            alert("Please fill in your name and email.");
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(userEmail)) {
            alert("Please enter a valid email address.");
            return;
        }

        fetch('php/book_event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                event_name: eventName,
                ticket_type: ticketType,
                user_name: userName,
                user_email: userEmail
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking successful!');
                localStorage.removeItem('ticketType');
                localStorage.removeItem('eventName');
                window.location.href = 'thankyou.html';
            } else {
                alert('Booking failed.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please try again later.');
        });
    });
});

