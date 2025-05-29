document.addEventListener('DOMContentLoaded', () => {
  // === User greeting and admin nav link ===
  const userName = localStorage.getItem('userName');
  const userRole = localStorage.getItem('userRole');
  const greeting = document.getElementById('greeting');
  const nav = document.getElementById('mainNav');

  if (greeting && userName) {
    greeting.textContent = `Hello, ${userName}! Welcome back to AASTU Events.`;
  }

  if (nav && userRole === 'admin') {
    const adminLink = document.createElement('a');
    adminLink.href = 'admin.php';
    adminLink.textContent = 'Admin Dashboard';
    adminLink.classList.add('nav-link');
    adminLink.style.fontWeight = 'bold';
    nav.appendChild(adminLink);
  }
if (nav && userRole === 'user') {
    const bookinglink = document.createElement('a');
    bookinglink.href = 'my_bookings.html';
    bookinglink.textContent = 'My Bookings';
    bookinglink.classList.add('nav-link');
    bookinglink.style.fontWeight = 'bold';
    nav.appendChild(bookinglink);
  }
  // === Load events dynamically from backend ===
  const eventsContainer = document.getElementById('eventsContainer');
  if (!eventsContainer) {
    console.error("Events container not found");
    return;
  }

  fetch('php/fetchEventforuser.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(events => {
      console.log('Events data received:', events);

      if (!events || events.length === 0) {
        eventsContainer.innerHTML = "<p>No upcoming events found.</p>";
        return;
      }

      eventsContainer.innerHTML = ''; // Clear previous content

      events.forEach(event => {
        const eventElement = document.createElement('div');
        eventElement.className = 'event';

        const startDate = new Date(event.start_date).toLocaleDateString('en-US', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });

        const endDate = event.end_date
          ? `<p><strong>End Date:</strong> ${new Date(event.end_date).toLocaleDateString('en-US', {
              weekday: 'long',
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            })}</p>`
          : '';
eventElement.classList.add('event-card'); // This adds the class to style the outer container
eventElement.innerHTML = `
  <div class="event-header">
    <div class="event-info">
      <p class="date">${startDate}</p>
      <p class="name">
        ${event.name}
        <img src="images/down-arrow.png" class="arrow-icon" alt="Toggle">
      </p>
    </div>
    <a href="tickets.html" class="tickets">Tickets</a>
  </div>
  <div class="details">
    <p><strong>Time:</strong> ${event.start_time.substring(0, 5)}${event.end_time && event.end_time !== '00:00:00' ? ' - ' + event.end_time.substring(0, 5) : ''}</p>
    <p><strong>Venue:</strong> ${event.venue}</p>
    ${endDate}
    <p>${event.description}</p>
  </div>
`;



        eventsContainer.appendChild(eventElement);

        // Add toggle functionality for this event
        const header = eventElement.querySelector(".event-header");
        header.addEventListener("click", () => {
          eventElement.classList.toggle("open");
          const arrowIcon = eventElement.querySelector(".arrow-icon");
          const isOpen = eventElement.classList.contains("open");
          arrowIcon.style.transform = isOpen ? "rotate(180deg)" : "rotate(0deg)";
        });
      });
    })
    .catch(err => {
      console.error("Error loading events:", err);
      eventsContainer.innerHTML = `<p class="error">Error loading events. Please try again later.</p>`;
    });

  // === Logo click redirect to home page ===
  const logo = document.getElementById('logo');
  if (logo) {
    logo.addEventListener('click', () => {
      window.location.href = 'index.html';
    });
  }

  // === Highlight current navigation link ===
  const currentPage = window.location.pathname.split("/").pop();
  const navLinks = document.querySelectorAll(".nav-link");
  navLinks.forEach(link => {
    if (link.getAttribute("href") === currentPage) {
      link.classList.add("active");
    }
  });

  // === Redirect buttons to ticket page ===
  const getTicketButton = document.getElementById('tic');
  if (getTicketButton) {
    getTicketButton.addEventListener('click', () => {
      window.location.href = 'tickets.html';
    });
  }

  const getTicsButton = document.getElementById('tics');
  if (getTicsButton) {
    getTicsButton.addEventListener('click', () => {
      window.location.href = 'tickets.html';
    });
  }
});
