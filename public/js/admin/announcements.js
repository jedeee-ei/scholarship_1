// Announcements Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    try {
        initializeAnnouncementsPage();
    } catch (error) {
        console.error('Error initializing announcements page:', error);
    }
});

function initializeAnnouncementsPage() {
    // Initialize search functionality
    initializeSearch();

    // Initialize form submission
    initializeFormSubmission();

    // Initialize modal close on outside click
    initializeModalEvents();
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.announcements-table tbody tr');

            tableRows.forEach(row => {
                if (row.querySelector('.no-data')) return; // Skip no-data row

                const title = row.querySelector('.announcement-title').textContent.toLowerCase();
                const content = row.querySelector('.content-preview').textContent.toLowerCase();

                if (title.includes(searchTerm) || content.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

// Form submission
function initializeFormSubmission() {
    const form = document.getElementById('announcementForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAnnouncementForm();
        });
    }
}

// Modal events
function initializeModalEvents() {
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('announcementModal');
        const confirmModal = document.getElementById('confirmModal');

        if (event.target === modal) {
            closeAnnouncementModal();
        }
        if (event.target === confirmModal) {
            closeConfirmModal();
        }
    });
}

// Open Add Announcement Modal
function openAddAnnouncementModal() {
    const modal = document.getElementById('announcementModal');
    const form = document.getElementById('announcementForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitText = document.getElementById('submitText');

    // Reset form
    form.reset();
    form.removeAttribute('data-announcement-id');

    // Set modal title and button text
    modalTitle.textContent = 'Add New Announcement';
    submitText.textContent = 'Create Announcement';



    // Show modal
    modal.style.display = 'block';

    // Focus on title field
    setTimeout(() => {
        document.getElementById('title').focus();
    }, 100);
}

// Edit Announcement
function editAnnouncement(id, title, content) {
    const modal = document.getElementById('announcementModal');
    const form = document.getElementById('announcementForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitText = document.getElementById('submitText');

    // Set form data
    form.setAttribute('data-announcement-id', id);
    document.getElementById('title').value = title;
    document.getElementById('content').value = content;

    // Set modal title and button text
    modalTitle.textContent = 'Edit Announcement';
    submitText.textContent = 'Update Announcement';

    // Show modal
    modal.style.display = 'block';

    // Focus on title field
    setTimeout(() => {
        document.getElementById('title').focus();
    }, 100);
}





// Delete Announcement
function deleteAnnouncement(id) {
    showConfirmModal(
        'Delete Announcement',
        'Are you sure you want to delete this announcement? This action cannot be undone.',
        'Delete',
        () => {
            fetch(`/admin/announcements/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success notification
                    if (window.showNotification) {
                        window.showNotification('success', data.message || 'Announcement deleted successfully!');
                    }
                    location.reload();
                } else {
                    console.error('Error deleting announcement:', data);
                    if (window.showNotification) {
                        window.showNotification('error', data.message || 'Error deleting announcement');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.showNotification) {
                    window.showNotification('error', 'Network error occurred. Please try again.');
                }
            });
        }
    );
}

// Submit Form
function submitAnnouncementForm() {
    const form = document.getElementById('announcementForm');
    const formData = new FormData(form);
    const announcementId = form.getAttribute('data-announcement-id');

    // Convert FormData to JSON
    const data = {
        title: formData.get('title'),
        content: formData.get('content')
    };

    const url = announcementId
        ? `/admin/announcements/${announcementId}`
        : '/admin/announcements/store';

    const method = announcementId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeAnnouncementModal();
            // Show success notification
            if (window.showNotification) {
                window.showNotification('success', data.message || 'Announcement saved successfully!');
            }
            location.reload();
        } else {
            console.error('Error saving announcement:', data);
            if (window.showNotification) {
                window.showNotification('error', data.message || 'Error saving announcement');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (window.showNotification) {
            window.showNotification('error', 'Network error occurred. Please try again.');
        }
    });
}

// Close Modals
function closeAnnouncementModal() {
    document.getElementById('announcementModal').style.display = 'none';
}

// Confirmation Modal Functions
function showConfirmModal(title, message, buttonText, onConfirm) {
    const modal = document.getElementById('confirmModal');
    const titleElement = document.getElementById('confirmTitle');
    const messageElement = document.getElementById('confirmMessage');
    const confirmButton = document.getElementById('confirmButton');

    titleElement.textContent = title;
    messageElement.textContent = message;
    confirmButton.textContent = buttonText;

    // Remove any existing event listeners
    const newConfirmButton = confirmButton.cloneNode(true);
    confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);

    // Add new event listener
    newConfirmButton.addEventListener('click', () => {
        closeConfirmModal();
        onConfirm();
    });

    modal.style.display = 'block';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}




