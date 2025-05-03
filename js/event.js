document.addEventListener('DOMContentLoaded', () => {
    const getTicketButton = document.getElementById('tic');
    const logo = document.getElementById('logo');
    const selectTicketButton = document.getElementById('tib');
    const popup = document.getElementById('popup');
    const cancel = document.getElementById('cancel');
    const button = document.getElementById('check');

    getTicketButton.addEventListener('click', () => {
        popup.style.display = 'block';
    });

    selectTicketButton.addEventListener('click', () => {
        popup.style.display = 'block';
    });

    cancel.addEventListener('click', () => {
        popup.style.display = 'none';
    });

    popup.addEventListener('click', (e) => {
        if (e.target === popup) {
            popup.style.display = 'none';
        }
    });

    logo.addEventListener('click', function () {
        window.location.href = 'index.html';
    });

    button.addEventListener('click', function () {
        var ticketType = document.getElementById('tics').value;
        var eventName = document.getElementById('eventName').innerText;
        localStorage.setItem('ticketType',ticketType);
        localStorage.setItem('eventName',eventName);
        window.location.href='checkout.html';
        fetch('php/book_event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                ticket_type: ticketType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking successful!');
                window.location.href = 'checkout.html';  
            } else {
                alert('Booking failed.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
