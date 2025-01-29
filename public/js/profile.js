// Change Profile Picture
const profileImage = document.getElementById('profileImage');
const changeProfilePicButton = document.getElementById('changeProfilePic');
const profilePicInput = document.createElement('input');
profilePicInput.type = 'file';
profilePicInput.accept = 'image/*';
profilePicInput.style.display = 'none';

document.body.appendChild(profilePicInput);

changeProfilePicButton.addEventListener('click', () => {
    profilePicInput.click();
});

profilePicInput.addEventListener('change', async function(event) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('Image size must be less than 2MB');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('profile_image', file);
            formData.append('_method', 'PUT');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const reader = new FileReader();
            reader.onload = function(e) {
                profileImage.src = e.target.result; // Show preview immediately
            };
            reader.readAsDataURL(file);

            const response = await fetch('/profile', {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to update profile picture');
            }

            if (data.success && data.image_path) {
                profileImage.src = data.image_path;
            }
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Failed to update profile picture');
            // Reload to show the previous image
            window.location.reload();
        }
    }
});

// Edit Profile functionality
let isEditMode = false;
const editProfileButton = document.getElementById('editProfile');
const detailItems = document.querySelectorAll('.detail-item p');

editProfileButton.addEventListener('click', async function() {
    if (!isEditMode) {
        // Switch to Edit Mode
        isEditMode = true;
        editProfileButton.textContent = 'Save Changes';

        detailItems.forEach(item => {
            const label = item.closest('.detail-item').querySelector('label');
            const fieldName = label.getAttribute('data-field');
            if (!fieldName) return;

            const input = document.createElement('input');
            input.type = fieldName === 'email' ? 'email' : 'text';
            input.value = item.textContent.trim();
            input.name = fieldName;
            input.required = (fieldName === 'full_name' || fieldName === 'email');
            
            item.innerHTML = '';
            item.appendChild(input);
        });
    } else {
        try {
            // Switch to View Mode and Save Changes
            const formData = new FormData();
            const inputs = document.querySelectorAll('.detail-item p input');
            
            inputs.forEach(input => {
                if (input.name && input.value) {
                    formData.append(input.name, input.value.trim());
                }
            });

            formData.append('_method', 'PUT');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const response = await fetch('/profile', {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to update profile');
            }

            if (data.success) {
                alert('Profile updated successfully!');
                window.location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Failed to update profile. Please try again.');
        }
    }
});