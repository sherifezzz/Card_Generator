// Change Profile Picture
const profileImage = document.getElementById('profileImage');
const changeProfilePicButton = document.getElementById('changeProfilePic');
const profilePicInput = document.createElement('input');
profilePicInput.type = 'file';
profilePicInput.accept = 'image/*';
profilePicInput.style.display = 'none';

changeProfilePicButton.addEventListener('click', () => {
    profilePicInput.click(); // Trigger file input
});

profilePicInput.addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            profileImage.src = e.target.result; // Update profile picture
        };
        reader.readAsDataURL(file);
    }
});

// Edit Profile Functionality
const editProfileButton = document.getElementById('editProfileButton');
const profileDetails = document.querySelectorAll('.profile-details p');
const detailItems = document.querySelectorAll('.detail-item p');

let isEditMode = false;

editProfileButton.addEventListener('click', function () {
    if (!isEditMode) {
        // Switch to Edit Mode
        isEditMode = true;
        editProfileButton.textContent = 'Save Changes';

        // Make profile details editable
        detailItems.forEach(item => {
            const currentValue = item.textContent;
            const input = document.createElement('input');
            input.type = 'text';
            input.value = currentValue;
            item.textContent = '';
            item.appendChild(input);
        });
    } else {
        // Switch to View Mode and Save Changes
        isEditMode = false;
        editProfileButton.textContent = 'Edit Profile';

        // Save changes and update profile details
        detailItems.forEach(item => {
            const input = item.querySelector('input');
            if (input) {
                item.textContent = input.value; // Update the displayed value
            }
        });

        // Simulate saving changes (you can replace this with an API call to save the data)
        alert('Profile updated successfully!');
    }
});