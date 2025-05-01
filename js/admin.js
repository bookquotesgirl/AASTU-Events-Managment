document.addEventListener("DOMContentLoaded", function () {
    // Handle Sidebar Navigation
    const menuLinks = document.querySelectorAll(".menu-link");
    const sections = document.querySelectorAll(".content-section");

    menuLinks.forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const targetId = this.getAttribute("href").substring(1);
            const targetSection = document.getElementById(targetId);

            // Remove 'active' from all sections
            sections.forEach(section => section.classList.remove("active"));

            // Add 'active' to the selected section
            if (targetSection) {
                targetSection.classList.add("active");

                // Update the browser URL without reloading
                history.pushState(null, null, `#${targetId}`);
            }
        });
    });

    // Handle Image Upload
    const uploadBox = document.getElementById("uploadBox");
    const imageUpload = document.getElementById("imageUpload");

    if (uploadBox && imageUpload) {
        uploadBox.addEventListener("click", function () {
            imageUpload.click();
        });

        imageUpload.addEventListener("change", function (event) {
            if (event.target.files.length > 0) {
                const file = event.target.files[0];
                const imageUrl = URL.createObjectURL(file);
        
                // Replace the upload box with an image and a delete button
                uploadBox.innerHTML = `
                    <div class="image-container">
                        <img src="${imageUrl}" style="width: 100%; height: 100%; object-fit: cover; border-radius:8px;">
                        <button class="delete-btn">🗑️</button>
                    </div>
                `;
        
                // Add event listener for delete button
                const deleteBtn = document.querySelector(".delete-btn");
                if (deleteBtn) {
                    deleteBtn.addEventListener("click", function (e) {
                        e.stopPropagation();
                        imageUpload.value = ""; 
                        uploadBox.innerHTML = `<span class="plus-sign">+</span>`; // Reset UI
                    });
                }
            }
        });
    }
    document.addEventListener('DOMContentLoaded', () => {
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.querySelector('.sidebar');
    
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    });

    // Select all Edit buttons
    const editButtons = document.querySelectorAll('#edit');
    // Select all Delete buttons
    const deleteButtons = document.querySelectorAll('#delete');

    // When Edit is clicked
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            alert('Edit feature coming soon!');
        });
    });

    // When Delete is clicked
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (confirm('Are you sure you want to delete this event?')) {
                alert('Delete feature coming soon!');
            }
        });
    });
    document.getElementById('save-settings').addEventListener('click', () => {
        alert('Settings saved (not really, just frontend demo!)');
    });
    
    
});
