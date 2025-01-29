// DOM Elements
const userList = document.querySelector('.user-list tbody');
const searchInput = document.getElementById('searchUser');
const searchButton = document.getElementById('searchButton');
const addUserButton = document.getElementById('addUserButton');

// Function to format date
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString();
}

// Load Users
async function loadUsers(searchTerm = '') {
    try {
        const response = await fetch(`/api/admin/users?search=${encodeURIComponent(searchTerm)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'Failed to fetch users');
        }

        populateUserList(data.users);
    } catch (error) {
        console.error('Error loading users:', error);
        showError('Failed to load users. Please try again.');
    }
}


// Populate User List
function populateUserList(users) {
    userList.innerHTML = '';

    users.forEach(user => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${user.username}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            <td>${formatDate(user.joined_date)}</td>
            <td>
                <button class="edit-user" data-id="${user.id}">Edit</button>
                <button class="delete-user" data-id="${user.id}">Delete</button>
                ${user.role !== 'admin' ? `<button class="promote-user" data-id="${user.id}">Promote to Admin</button>` : ''}
            </td>
        `;
        userList.appendChild(row);
    });

    addUserActionListeners();
}

// Search Functionality
searchInput.addEventListener('input', function () {
    loadUsers(this.value.trim());
});

// Create modal elements
function createModalStructure() {
    const modalHtml = `
        <div id="userModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modalTitle">Add New User</h2>
                <form id="userForm">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group password-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="full_name">Full Name:</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <input type="text" id="location" name="location">
                    </div>
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="submit-btn">Save</button>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// Add CSS Styles
function addModalStyles() {
    const modalStyles = `
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .submit-btn {
            background-color: #4a5568;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .submit-btn:hover {
            background-color: #2d3748;
        }

        .success-message,
        .error-message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 16px;
            text-align: center;
        }

        .success-message {
            background-color: #c6f6d5;
            border: 1px solid #68d391;
            color: #2f855a;
        }

        .error-message {
            background-color: #fed7d7;
            border: 1px solid #f56565;
            color: #c53030;
        }
    `;

    const styleSheet = document.createElement('style');
    styleSheet.textContent = modalStyles;
    document.head.appendChild(styleSheet);
}

// Show Add User Modal
function showAddUserModal() {
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const modalTitle = document.getElementById('modalTitle');
    const passwordGroup = document.querySelector('.password-group');

    modalTitle.textContent = 'Add New User';
    form.reset();
    passwordGroup.style.display = 'block';
    modal.style.display = 'flex';

    form.onsubmit = async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(form);
            const response = await fetch('/api/admin/users', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            if (!response.ok) {
                throw new Error('Failed to add user');
            }

            await loadUsers();
            modal.style.display = 'none';
            showSuccess('User added successfully');
        } catch (error) {
            console.error('Error adding user:', error);
            showError('Failed to add user. Please try again.');
        }
    };
}

// Show Edit User Modal
async function showEditUserModal(userId) {
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const modalTitle = document.getElementById('modalTitle');
    const passwordGroup = document.querySelector('.password-group');

    try {
        const response = await fetch(`/api/admin/users/${userId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'Failed to fetch user details');
        }

        const user = data.user;

        modalTitle.textContent = 'Edit User';
        passwordGroup.style.display = 'none';

        // Fill form with user data
        form.username.value = user.username;
        form.email.value = user.email;
        form.full_name.value = user.full_name;
        form.phone.value = user.phone || '';
        form.location.value = user.location || '';
        form.role.value = user.role;

        modal.style.display = 'flex';

        form.onsubmit = async (e) => {
            e.preventDefault();
            try {
                const formData = new FormData(form);
                formData.delete('password'); // Remove password from edit form

                const response = await fetch(`/api/admin/users/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (!result.success) {
                    throw new Error(result.message || 'Failed to update user');
                }

                await loadUsers();
                modal.style.display = 'none';
                showSuccess('User updated successfully');
            } catch (error) {
                console.error('Error updating user:', error);
                showError('Failed to update user. Please try again.');
            }
        };
    } catch (error) {
        console.error('Error fetching user details:', error);
        showError('Failed to load user details. Please try again.');
    }
}

// User Actions Event Listeners
function addUserActionListeners() {
    // Edit User
    document.querySelectorAll('.edit-user').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.id;
            showEditUserModal(userId);
        });
    });

    // Delete User
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', async function () {
            const userId = this.dataset.id;
            const username = this.closest('tr').querySelector('td').textContent;

            if (confirm(`Are you sure you want to delete user: ${username}?`)) {
                try {
                    const response = await fetch(`/api/admin/users/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to delete user');
                    }

                    loadUsers(searchInput.value.trim());
                    showSuccess('User deleted successfully');
                } catch (error) {
                    console.error('Error deleting user:', error);
                    showError('Failed to delete user. Please try again.');
                }
            }
        });
    });

    // Promote User
    document.querySelectorAll('.promote-user').forEach(button => {
        button.addEventListener('click', async function () {
            const userId = this.dataset.id;
            const username = this.closest('tr').querySelector('td').textContent;

            if (confirm(`Are you sure you want to promote user: ${username} to Admin?`)) {
                try {
                    const response = await fetch(`/api/admin/users/${userId}/promote`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to promote user');
                    }

                    loadUsers(searchInput.value.trim());
                    showSuccess('User promoted to admin successfully');
                } catch (error) {
                    console.error('Error promoting user:', error);
                    showError('Failed to promote user. Please try again.');
                }
            }
        });
    });
}

// Modal close functionality
function setupModalClose() {
    const modal = document.getElementById('userModal');
    const closeBtn = modal.querySelector('.close');

    closeBtn.onclick = () => {
        modal.style.display = 'none';
    };

    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

// Show Success Message
function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;
    document.querySelector('.admin-user-management').prepend(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

// Show Error Message
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    document.querySelector('.admin-user-management').prepend(errorDiv);
    setTimeout(() => errorDiv.remove(), 3000);
}

// Add New User button event listener
addUserButton.addEventListener('click', showAddUserModal);

// Initialize everything
function initialize() {
    createModalStructure();
    addModalStyles();
    setupModalClose();
    loadUsers();
}

// Run initialization when the page loads
window.addEventListener('load', initialize);