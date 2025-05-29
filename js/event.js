document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const eventId = urlParams.get('id');
  const bookingContainer = document.getElementById('booking-link-container');

  // Add Book Now link if event ID exists
  if (eventId && bookingContainer) {
    const bookingLink = document.createElement('a');
    bookingLink.href = `booking.html?id=${eventId}`;
    bookingLink.textContent = 'Book Now';
    bookingLink.classList.add('book-btn');
    bookingContainer.appendChild(bookingLink);
  }

  // Handle missing event ID
  if (!eventId) {
    document.getElementById('first').innerHTML = '<p>Event ID missing in URL.</p>';
    return;
  }

  // Fetch event details
  fetch(`php/fetchSingleEvent.php?id=${eventId}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        document.getElementById('first').innerHTML = `<p>${data.error}</p>`;
        return;
      }

      // Debugging: Log the received data
      console.log("Event data received:", data);
      
      // Set event name
      document.getElementById('eventName').textContent = data.name;

      // Format date and time
      const startDateTime = new Date(`${data.start_date}T${data.start_time}`);
      document.getElementById('eventDate').textContent = startDateTime.toLocaleString();
      document.getElementById('eventTimeLocation').textContent = `${startDateTime.toLocaleString()} at ${data.venue}`;

      // Set event image with proper path handling
      const eventImg = document.getElementById('eventImage');
      if (data.image) {
        let imagePath = data.image.toString(); // Ensure it's a string
        imagePath = imagePath.replace(/\\/g, '/'); // Normalize slashes
        
        // Add base path if needed
        if (!imagePath.startsWith('http') && 
            !imagePath.startsWith('/') && 
            !imagePath.startsWith('images/') && 
            !imagePath.startsWith('php/')) {
          imagePath = 'images/' + imagePath;
        }
        
        console.log("Final image path:", imagePath); // Debug path
        eventImg.src = imagePath;
        eventImg.alt = data.name || 'Event Image';
        eventImg.onerror = () => {
          console.warn("Failed to load image, using fallback");
          eventImg.src = 'images/default.jpg';
        };
      } else {
        console.warn("No image path provided, using default");
        eventImg.src = 'images/default.jpg';
      }

      // Set event description
      document.getElementById('aboutEvent').textContent = data.description || 'No description available.';

      // Populate ticket table
      const ticketBody = document.getElementById('ticketTableBody');
      ticketBody.innerHTML = '';
      if (data.tickets && data.tickets.length > 0) {
        data.tickets.forEach(ticket => {
          ticketBody.innerHTML += `
            <tr>
              <td>${ticket.name}</td>
              <td>${ticket.price} birr</td>
            </tr>`;
        });
      } else {
        ticketBody.innerHTML = '<tr><td colspan="2">No tickets available.</td></tr>';
      }
    })
    .catch(error => {
      console.error('Error fetching event:', error);
      document.getElementById('first').innerHTML = '<p>Failed to load event details.</p>';
    });
});