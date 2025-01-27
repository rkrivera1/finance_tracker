document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('finance-tracker-admin-form');
	
	form.addEventListener('submit', function(e) {
		e.preventDefault();
		
		const formData = {
			stockApiKey: document.getElementById('stock-api-key').value,
			stockApiProvider: document.getElementById('stock-api-provider').value,
			updateInterval: document.getElementById('update-interval').value
		};

		// Save settings using Nextcloud API
		OCP.AppConfig.setValue('finance_tracker', 'stock_api_key', formData.stockApiKey);
		OCP.AppConfig.setValue('finance_tracker', 'stock_api_provider', formData.stockApiProvider);
		OCP.AppConfig.setValue('finance_tracker', 'update_interval', formData.updateInterval);

		// Show success message
		OC.Notification.showTemporary(t('finance_tracker', 'Settings saved successfully'));
	});
});