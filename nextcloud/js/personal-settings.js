document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('finance-tracker-personal-form');
    
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(form);
        const settings = {};

        for (const [key, value] of formData.entries()) {
            settings[key] = value;
        }

        try {
            const response = await fetch(OC.generateUrl('/apps/finance_tracker/settings/personal'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify(settings)
            });

            if (response.ok) {
                OC.Notification.showTemporary('Settings saved successfully');
            } else {
                const errorData = await response.json();
                OC.Notification.showTemporary(errorData.message || 'Failed to save settings');
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            OC.Notification.showTemporary('An error occurred while saving settings');
        }
    });
});
