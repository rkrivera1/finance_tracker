<?php
script('finance_tracker', 'settings');
style('finance_tracker', 'settings');

/** @var \OCP\IL10N $l */
/** @var array $_ */
?>

<div class="section" id="finance-tracker-personal-settings">
    <h2><?php p($l->t('Finance Tracker Personal Settings')); ?></h2>

    <form id="finance-tracker-personal-form">
        <div class="section-block">
            <h3><?php p($l->t('Personal Preferences')); ?></h3>
            
            <div class="settings-input">
                <label for="preferred-currency"><?php p($l->t('Preferred Currency')); ?></label>
                <select id="preferred-currency" name="preferred_currency">
                    <option value="USD" <?php p($_['preferred_currency'] === 'USD' ? 'selected' : ''); ?>>USD</option>
                    <option value="EUR" <?php p($_['preferred_currency'] === 'EUR' ? 'selected' : ''); ?>>EUR</option>
                    <option value="GBP" <?php p($_['preferred_currency'] === 'GBP' ? 'selected' : ''); ?>>GBP</option>
                    <option value="JPY" <?php p($_['preferred_currency'] === 'JPY' ? 'selected' : ''); ?>>JPY</option>
                </select>
            </div>

            <div class="settings-input">
                <label for="budget-notification-threshold"><?php p($l->t('Budget Notification Threshold (%)')); ?></label>
                <input type="number" 
                       id="budget-notification-threshold" 
                       name="budget_notification_threshold" 
                       min="50" 
                       max="100" 
                       value="<?php p($_['budget_notification_threshold'] ?? '80'); ?>"
                >
                <p class="settings-hint">
                    <?php p($l->t('Percentage of budget at which you will receive notifications')); ?>
                </p>
            </div>
        </div>

        <div class="section-block">
            <h3><?php p($l->t('Notifications')); ?></h3>
            
            <div class="settings-input">
                <input type="checkbox" 
                       id="budget-notifications" 
                       name="budget_notifications"
                       <?php p($_['budget_notifications'] ? 'checked' : ''); ?>
                >
                <label for="budget-notifications"><?php p($l->t('Enable Budget Notifications')); ?></label>
            </div>

            <div class="settings-input">
                <input type="checkbox" 
                       id="transaction-notifications" 
                       name="transaction_notifications"
                       <?php p($_['transaction_notifications'] ? 'checked' : ''); ?>
                >
                <label for="transaction-notifications"><?php p($l->t('Enable Transaction Notifications')); ?></label>
            </div>
        </div>

        <div class="section-block">
            <h3><?php p($l->t('Privacy')); ?></h3>
            
            <div class="settings-input">
                <input type="checkbox" 
                       id="anonymize-personal-data" 
                       name="anonymize_personal_data"
                       <?php p($_['anonymize_personal_data'] ? 'checked' : ''); ?>
                >
                <label for="anonymize-personal-data"><?php p($l->t('Anonymize Personal Financial Data')); ?></label>
                <p class="settings-hint">
                    <?php p($l->t('Remove personal identifiers from financial reports')); ?>
                </p>
            </div>
        </div>

        <div class="section-actions">
            <button type="submit" class="primary">
                <?php p($l->t('Save Changes')); ?>
            </button>
            <div class="save-status" id="save-status"></div>
        </div>
    </form>
</div>
