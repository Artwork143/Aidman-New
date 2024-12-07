document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.querySelector('.dropdown i');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    // Handle dropdown menu
    userIcon.addEventListener('click', function() {
        dropdownMenu.classList.toggle('show');
    });

    document.addEventListener('click', function(event) {
        if (!userIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });


    // JavaScript for logout confirmation
    const logoutLink = document.getElementById('logout-link');
    const modal = document.getElementById('logout-modal');
    const closeModal = document.querySelector('#logout-modal .close');
    const confirmLogout = document.getElementById('confirm-logout');
    const cancelLogout = document.getElementById('cancel-logout');

    if (logoutLink) {
        logoutLink.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent immediate navigation
            modal.style.display = 'block'; // Show modal
        });
    }

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none'; // Hide modal
    });

    cancelLogout.addEventListener('click', () => {
        modal.style.display = 'none'; // Hide modal
    });

    confirmLogout.addEventListener('click', () => {
        window.location.href = logoutLink.href; // Proceed with logout
    });

    // Close the modal if clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none'; // Hide modal
        }
    });
});

// Function to load account info data when clicking "Account Information"
document.getElementById('view-profile').addEventListener('click', function (e) {
    e.preventDefault();
    fetch('account-information.php')
        .then(response => response.text())
        .then(data => {
            // Here you would typically populate the account information page with the user data
            window.location.href = 'account-information.php';
        })
        .catch(error => {
            console.error('Error loading profile:', error);
        });
});

// Sweet Alert For Offcial-Aid-dashboard.php
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default action of the link
        const id = this.getAttribute('official-data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to edit this entry?",
            icon: 'info',
            background: '#f4f4f4',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fas fa-check"></i> Yes, edit it!',
            cancelButtonText: '<i class="fas fa-times"></i> No, cancel!',
            showCancelButton: true, // Show the "No" button
            customClass: {
                container: 'swal2-container',
                title: 'swal2-title',
                content: 'swal2-content',
                confirmButton: 'swal2-confirm',
                cancelButton: 'swal2-cancel'
            }
            
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `official-edit-data.php?id=${id}`;
            }
        });
    });
});

document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default action of the link
        const id = this.getAttribute('official-data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'error',
            background: '#f4f4f4',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Yes, delete it!',
            cancelButtonText: '<i class="fas fa-ban"></i> No, cancel!',
            showCancelButton: true, // Show the "No" button
            customClass: {
                container: 'swal2-container',
                title: 'swal2-title',
                content: 'swal2-content',
                confirmButton: 'swal2-confirm',
                cancelButton: 'swal2-cancel'
            },
        
            
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `official-delete-data.php?id=${id}`;
            }
        });
    });
});