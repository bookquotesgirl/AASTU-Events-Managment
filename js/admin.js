document.addEventListener("DOMContentLoaded", function () {
  console.log("Add Ticket button:", document.getElementById('add-ticket'));

  // --- Sidebar Toggle ---
  const menuToggle = document.getElementById("menu-toggle");
  const sidebar = document.querySelector(".sidebar");
  const mainContent = document.querySelector(".main-content");

  if (menuToggle) {
    menuToggle.addEventListener("click", function () {
      sidebar.classList.toggle("active");
      mainContent.classList.toggle("shifted");
    });
  }

  // Check if bookings section is active on page load
  if (window.location.hash === '#view-bookings' || 
      document.querySelector('.menu-link[href="#view-bookings"]')?.classList.contains('active')) {
    initBookingSection();
  }

  // --- Section Navigation ---
  const menuLinks = document.querySelectorAll(".menu-link");
  const sections = document.querySelectorAll(".content-section");

  menuLinks.forEach(link => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      menuLinks.forEach(item => item.classList.remove("active"));
      sections.forEach(section => section.classList.remove("active"));
      this.classList.add("active");
      const sectionId = this.getAttribute("href").substring(1);
      if (sectionId === 'view-bookings') {
        initBookingSection();
      }
      const targetSection = document.getElementById(sectionId);
      if (targetSection) targetSection.classList.add("active");
    });
  });

  // --- Form Stepper Logic ---
  const steps = document.querySelectorAll(".form-step");
  const stepButtons = document.querySelectorAll(".form-navigation button");
  const stepIndicators = document.querySelectorAll(".step");
  let currentStep = 0;

  function showStep(index) {
    steps.forEach((step, i) => {
      step.classList.toggle("active", i === index);
      stepIndicators[i].classList.toggle("active", i === index);
    });
  }

  stepButtons.forEach(button => {
    button.addEventListener("click", () => {
      if (button.classList.contains("btn-next") && currentStep < steps.length - 1) currentStep++;
      if (button.classList.contains("btn-prev") && currentStep > 0) currentStep--;
      showStep(currentStep);
    });
  });

  // --- Image Preview ---
  const imageUpload = document.getElementById("imageUpload");
  const imagePreview = document.getElementById("imagePreview");

  if (imageUpload) {
    imageUpload.addEventListener("change", () => {
      const file = imageUpload.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = () => {
          imagePreview.innerHTML = `<img src="${reader.result}" alt="Event Image Preview" />`;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // --- Event Type Fields Toggle ---
  const eventTypeRadios = document.querySelectorAll("input[name='event_type']");
  const physicalLocation = document.getElementById("physical-location");
  const onlineDetails = document.getElementById("online-details");

  function updateEventTypeFields(value) {
    const onlineUrlInput = document.getElementById("online-url");
    if (value === "physical") {
      physicalLocation.style.display = "block";
      onlineDetails.style.display = "none";
      onlineUrlInput.required = false;
      onlineUrlInput.disabled = true;
    } else {
      physicalLocation.style.display = "none";
      onlineDetails.style.display = "block";
      onlineUrlInput.required = true;
      onlineUrlInput.disabled = false;
    }
  }

  eventTypeRadios.forEach(radio => {
    radio.addEventListener("change", (e) => updateEventTypeFields(e.target.value));
  });

  const checkedRadio = document.querySelector("input[name='event_type']:checked");
  if (checkedRadio) updateEventTypeFields(checkedRadio.value);

  // --- Registration Type Logic ---
  const registrationRadios = document.querySelectorAll("input[name='registration_type']");
  const ticketOptions = document.getElementById("ticket-options");
  const ticketList = document.getElementById("ticket-list");
  const addTicketBtn = document.getElementById("add-ticket");

  // Initialize ticket options visibility
  updateTicketOptionsVisibility();

  // Update when registration type changes
  registrationRadios.forEach(radio => {
    radio.addEventListener("change", updateTicketOptionsVisibility);
  });

  // Add ticket functions
  if (addTicketBtn) {
    addTicketBtn.addEventListener("click", () => {
      const selectedType = document.querySelector("input[name='registration_type']:checked").value;
      addTicket(selectedType);
    });
  }

  function updateTicketOptionsVisibility() {
    const selectedRadio = document.querySelector("input[name='registration_type']:checked");
    if (!selectedRadio) return;
    
    const selectedType = selectedRadio.value;
    ticketOptions.style.display = (selectedType === 'free' || selectedType === 'paid') ? 'block' : 'none';
  }

  function addTicket(ticketType) {
    const ticketId = Date.now();
    const ticketHtml = `
      <div class="ticket-item" data-ticket-id="${ticketId}">
        <input type="hidden" name="ticket_type[]" value="${ticketType}">
        
        <div class="form-group">
          <input type="text" name="ticket_name[]" placeholder="Ticket Name" required>
        </div>
        
        ${ticketType === 'paid' ? `
        <div class="form-group">
          <input type="number" name="ticket_price[]" placeholder="Price" min="0.01" step="0.01" required>
        </div>
        ` : `<input type="hidden" name="ticket_price[]" value="0">`}
        
        <div class="form-group">
          <input type="number" name="ticket_quantity[]" placeholder="Quantity" min="1" required>
        </div>
        
        <button type="button" class="remove-ticket">
          <i class="fas fa-trash"></i> Remove
        </button>
        
        <span class="ticket-type-badge ${ticketType}">
          ${ticketType === 'paid' ? 'Paid' : 'Free'}
        </span>
      </div>`;
    
    ticketList.insertAdjacentHTML("beforeend", ticketHtml);
  }

  // Ticket removal
  if (ticketList) {
    ticketList.addEventListener("click", (e) => {
      if (e.target.classList.contains("remove-ticket") || 
        e.target.closest(".remove-ticket")) {
        const target = e.target.classList.contains("remove-ticket") 
          ? e.target 
          : e.target.closest(".remove-ticket");
        target.closest(".ticket-item").remove();
      }
    });
  }

  // --- Short Description Count ---
  const descInput = document.getElementById("short-description");
  const descCount = document.getElementById("desc-count");
  if (descInput && descCount) {
    descInput.addEventListener("input", (e) => {
      descCount.textContent = e.target.value.length;
    });
  }

  // --- Fetch and Render Events ---
  function fetchEvents() {
    function capitalize(str) {
      if (typeof str !== 'string') return '';
      return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
    }

    function setupActions() {
      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          window.location.href = `edit_event.php?id=${btn.dataset.id}`;
        });
      });

      document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          window.location.href = `view_event.php?id=${btn.dataset.id}`;
        });
      });

      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          if (confirm('Are you sure you want to delete this event?')) {
            fetch(`php/delete_event.php?id=${btn.dataset.id}`, { method: 'DELETE' })
              .then(res => res.text())
              .then(() => fetchEvents());
          }
        });
      });
    }

    fetch('php/fetch_events.php')
      .then(res => res.json())
      .then(data => {
        const tbody = document.querySelector('.data-table tbody');
        if (!tbody) {
          alert('Table body not found');
          return;
        }
        tbody.innerHTML = '';
        if (data.length === 0) {
          tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;">No events found</td></tr>`;
        } else {
          data.forEach(event => {
            const basePath = 'php/';
            const imageSrc = event.image ? basePath + event.image : 'images/default.png';

            const eventName = event.name || 'Untitled';
            const category = event.category || 'N/A';
            const eventType = event.event_type ? capitalize(event.event_type) : 'N/A';
            const status = event.status ? event.status : 'Unknown';
            const statusClass = status.toLowerCase();
            const registrations = event.registrations ?? 0;
            const capacity = event.capacity && event.capacity !== '0' ? event.capacity : '∞';
            const startDate = event.start_date ? formatDate(event.start_date) : 'TBD';
            const startTime = event.start_time || '';

            const row = document.createElement('tr');
            row.innerHTML = `
              <td><input type="checkbox"></td>
              <td>
                <div class="event-info">
                  <img src="${imageSrc}" alt="Event" class="event-thumb">
                  <span>${eventName}</span>
                </div>
              </td>
              <td>${startDate}<br>${startTime}</td>
              <td>${category}</td>
              <td>${eventType}</td>
              <td><span class="status-badge ${statusClass}">${status}</span></td>
              <td>${registrations}/${capacity}</td>
              <td>
                <div class="action-buttons">
                  <button class="btn-icon edit-btn" data-id="${event.id}"><i class="fas fa-edit"></i></button>
                  <button class="btn-icon view-btn" data-id="${event.id}"><i class="fas fa-eye"></i></button>
                  <button class="btn-icon delete-btn" data-id="${event.id}"><i class="fas fa-trash-alt"></i></button>
                </div>
              </td>
            `;
            tbody.appendChild(row);
          });
        }
        setupActions();
      })
      .catch(err => {
        console.error('Fetch error:', err);
      });
  }

  // --- Booking Filters and Fetch ---
  function fetchBookings() {
    // Define params here
    const params = new URLSearchParams({
      event: document.getElementById('event-select')?.value || 'all',
      status: document.getElementById('status-filter')?.value || 'all',
      dateFrom: document.getElementById('date-from')?.value || '',
      dateTo: document.getElementById('date-to')?.value || ''
    });

    fetch(`php/admin_get_bookings.php?${params.toString()}`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        const tableBody = document.getElementById('booking-list');
        if (!tableBody) return;
        
        tableBody.innerHTML = '';
        
        if (!data.data || data.data.length === 0) {
          tableBody.innerHTML = '<tr><td colspan="7">No bookings found</td></tr>';
          return;
        }

        data.data.forEach(booking => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${booking.id || 'N/A'}</td>
            <td>${booking.event_name || 'N/A'}</td>
            <td>${booking.attendee_name || 'N/A'}</td>
            <td>${booking.ticket_name || 'N/A'}<br>
                <small>${booking.ticket_price ? '$'+booking.ticket_price : 'Free'}</small>
            </td>
            <td>${booking.booking_date ? new Date(booking.booking_date).toLocaleDateString() : 'N/A'}</td>
            <td><span class="status-badge ${booking.status ? booking.status.toLowerCase() : 'pending'}">
                ${booking.status || 'Pending'}
              </span>
            </td>
            
          `;
          tableBody.appendChild(row);
        });
      })
      .catch(error => {
        console.error('Error:', error);
        const tableBody = document.getElementById('booking-list');
        if (tableBody) {
          tableBody.innerHTML = `<tr><td colspan="7">Error loading bookings</td></tr>`;
        }
      });
  }

  function initBookingSection() {
    // Populate event select dropdown
    fetch('php/get_events.php')
      .then(res => res.json())
      .then(events => {
        const eventSelect = document.getElementById('event-select');
        if (!eventSelect) return;
        
        eventSelect.innerHTML = '<option value="all">All Events</option>';
        
        events.forEach(event => {
          const option = document.createElement('option');
          option.value = event.id;
          option.textContent = event.name;
          eventSelect.appendChild(option);
        });
        
        // Now that filters are ready, fetch bookings
        fetchBookings();
      })
      .catch(err => {
        console.error('Error fetching events:', err);
        fetchBookings(); // Still try to fetch bookings even if events fail
      });

    // Add event listeners to filters
    const filters = ['event-select', 'status-filter', 'date-from', 'date-to'];
    filters.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.addEventListener('change', fetchBookings);
    });
  }

  // Remove duplicate filter event listeners
  // (The ones at the bottom of the original code were removed)

  // Initial fetch of events if needed
  // fetchEvents();
});