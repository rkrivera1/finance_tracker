document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('finance-tracker-admin-form');
    
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(form);
        const settings = {};

        for (const [key, value] of formData.entries()) {
            // Convert checkboxes to boolean
            if (value === 'on') {
                settings[key] = 'true';
            } else {
                settings[key] = value;
            }
        }

        try {
            const response = await fetch(OC.generateUrl('/apps/finance_tracker/settings/admin'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify(settings)
            });

            if (response.ok) {
                OC.Notification.showTemporary('Admin settings saved successfully');
            } else {
                const errorData = await response.json();
                OC.Notification.showTemporary(errorData.message || 'Failed to save admin settings');
            }
        } catch (error) {
            console.error('Error saving admin settings:', error);
            OC.Notification.showTemporary('An error occurred while saving admin settings');
        }
    });
});
