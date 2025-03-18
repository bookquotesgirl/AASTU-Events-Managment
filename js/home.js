document.addEventListener('DOMContentLoaded', () => {
  // Toggle event details
  document.querySelectorAll(".event").forEach(event => {
      event.addEventListener("click", function () {
          const details = this.querySelector(".details");

          if (details.style.maxHeight && details.style.maxHeight !== "0px") {
              details.style.maxHeight = "0px"; // Collapse
              this.classList.remove("open");
          } else {
              details.style.maxHeight = details.scrollHeight + "px"; // Expand
              this.classList.add("open");
          }
      });
  });

  // Toggle event details when clicking event name
  document.querySelectorAll('.name').forEach((name) => {
      name.addEventListener('click', (event) => {
          event.stopPropagation(); // Prevents triggering the parent `.event` click
          const details = name.closest('.event').querySelector('.details');

          if (details.style.maxHeight && details.style.maxHeight !== "0px") {
              details.style.maxHeight = "0px"; // Collapse
              name.closest('.event').classList.remove("open");
          } else {
              details.style.maxHeight = details.scrollHeight + "px"; // Expand
              name.closest('.event').classList.add("open");
          }
      });
  });

  // Logo click redirect to home page
  const logo = document.getElementById('logo');
  if (logo) {
      logo.addEventListener('click', () => {
          window.location.href = 'index.html';
      });
  }

  // Highlight current navigation link
  const currentPage = window.location.pathname.split("/").pop();
  const navLinks = document.querySelectorAll(".nav-link");

  navLinks.forEach(link => {
      if (link.getAttribute("href") === currentPage) {
          link.classList.add("active");
      }
  });

  // Redirect buttons to ticket page
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
