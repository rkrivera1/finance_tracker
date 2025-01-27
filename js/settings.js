document.addEventListener('DOMContentLoaded', function() {
    const adminForm = document.getElementById('finance-tracker-admin-form');
    const personalForm = document.getElementById('finance-tracker-personal-form');
    const saveStatus = document.getElementById('save-status');

    function showStatus(message, isSuccess = true) {
        saveStatus.textContent = message;
        saveStatus.className = isSuccess ? 'save-status success' : 'save-status error';
        setTimeout(() => {
            saveStatus.textContent = '';
            saveStatus.className = 'save-status';
        }, 3000);
    }

    function serializeForm(form) {
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            // Convert checkboxes to boolean
            data[key] = value === 'on' ? true : value;
        }
        return data;
    }

    if (adminForm) {
        adminForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = serializeForm(adminForm);

            fetch(OC.generateUrl('/apps/finance_tracker/settings/admin'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showStatus('Admin settings saved successfully');
                } else {
                    showStatus(data.message || 'Error saving settings', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('Network error. Please try again.', false);
            });
        });
    }

    if (personalForm) {
        personalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = serializeForm(personalForm);

            fetch(OC.generateUrl('/apps/finance_tracker/settings/personal'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showStatus('Personal settings saved successfully');
                } else {
                    showStatus(data.message || 'Error saving settings', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('Network error. Please try again.', false);
            });
        });
    }
});
