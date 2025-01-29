// DOM Elements
const cardList = document.querySelector('.card-list tbody');
const searchInput = document.getElementById('searchCard');
const searchButton = document.getElementById('searchButton');

// Load Cards
async function loadCards(searchTerm = '') {
    try {
        const response = await fetch(`/api/admin/cards?search=${encodeURIComponent(searchTerm)}`, {
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
            throw new Error(data.message || 'Failed to fetch cards');
        }

        populateCardList(data.cards);
    } catch (error) {
        console.error('Error loading cards:', error);
        showError('Failed to load cards. Please try again.');
    }
}

// Populate Card List
function populateCardList(cards) {
    cardList.innerHTML = '';

    cards.forEach(card => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${card.title}</td>
            <td>${card.createdBy}</td>
            <td>${card.creationDate}</td>
            <td>${card.status}</td>
            <td>
                <button class="view-card" data-id="${card.id}">View</button>
                <button class="delete-card" data-id="${card.id}">Delete</button>
            </td>
        `;
        cardList.appendChild(row);
    });

    addCardActionListeners();
}

// Search Functionality
let searchTimeout;
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadCards(this.value.trim());
    }, 300);
});

// Card Actions
function addCardActionListeners() {
    // View Card
    document.querySelectorAll('.view-card').forEach(button => {
        button.addEventListener('click', async function() {
            const cardId = this.dataset.id;
            try {
                const response = await fetch(`/api/admin/cards/${cardId}`, {
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
                    throw new Error(data.message || 'Failed to fetch card details');
                }

                showCardDetails(data.card);
            } catch (error) {
                console.error('Error viewing card:', error);
                showError('Failed to load card details. Please try again.');
            }
        });
    });

    // Delete Card
    document.querySelectorAll('.delete-card').forEach(button => {
        button.addEventListener('click', async function() {
            const cardId = this.dataset.id;
            const cardTitle = this.closest('tr').querySelector('td').textContent;
            
            if (confirm(`Are you sure you want to delete card: ${cardTitle}?`)) {
                try {
                    const response = await fetch(`/api/admin/cards/${cardId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    if (!result.success) {
                        throw new Error(result.message || 'Failed to delete card');
                    }

                    await loadCards(searchInput.value.trim());
                    showSuccess('Card deleted successfully');
                } catch (error) {
                    console.error('Error deleting card:', error);
                    showError('Failed to delete card. Please try again.');
                }
            }
        });
    });
}

// Show Card Details Modal
function showCardDetails(card) {
    const modalHtml = `
        <div class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Card Details</h2>
                <div class="card-details">
                    <p><strong>Card Name:</strong> ${card.card_name}</p>
                    <p><strong>Full Name:</strong> ${card.full_name}</p>
                    <p><strong>Created By:</strong> ${card.user.username}</p>
                    <p><strong>Email:</strong> ${card.email || 'N/A'}</p>
                    <p><strong>Phone:</strong> ${card.phone || 'N/A'}</p>
                    <p><strong>Address:</strong> ${card.address || 'N/A'}</p>
                    <p><strong>Created At:</strong> ${new Date(card.created_at).toLocaleDateString()}</p>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = document.querySelector('.modal');
    const closeBtn = modal.querySelector('.close');

    closeBtn.onclick = () => modal.remove();
    window.onclick = (event) => {
        if (event.target === modal) modal.remove();
    };
}

// Show Success Message
function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;
    document.querySelector('.admin-card-management').prepend(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

// Show Error Message
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    document.querySelector('.admin-card-management').prepend(errorDiv);
    setTimeout(() => errorDiv.remove(), 3000);
}

// Add CSS Styles
const style = document.createElement('style');
style.textContent = `
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

    .card-details p {
        margin: 10px 0;
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
document.head.appendChild(style);

// Initialize card list when the page loads
window.addEventListener('load', () => loadCards());