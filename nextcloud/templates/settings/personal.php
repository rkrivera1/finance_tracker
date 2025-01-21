<?php
/** @var array $_ */
/** @var \OCP\IL10N $l */
script('finance_tracker', 'personal-settings');
style('finance_tracker', 'personal-settings');
?>

<div class="section" id="finance-tracker-personal-settings">
    <h2><?php p($l->t('Finance Tracker Personal Settings')); ?></h2>

    <form id="finance-tracker-personal-form">
        <div class="form-group">
            <label for="default-currency"><?php p($l->t('Default Currency')); ?></label>
            <select id="default-currency" name="default_currency">
                <option value="USD" <?php p($_['default_currency'] === 'USD' ? 'selected' : ''); ?>>USD</option>
                <option value="EUR" <?php p($_['default_currency'] === 'EUR' ? 'selected' : ''); ?>>EUR</option>
                <option value="GBP" <?php p($_['default_currency'] === 'GBP' ? 'selected' : ''); ?>>GBP</option>
                <!-- Add more currency options -->
            </select>
        </div>

        <div class="form-group">
            <label for="transaction-categories"><?php p($l->t('Transaction Categories')); ?></label>
            <textarea id="transaction-categories" name="transaction_categories" rows="4"><?php 
                p(json_encode(
                    json_decode($_['transaction_categories'], true), 
                    JSON_PRETTY_PRINT
                )); 
            ?></textarea>
            <small><?php p($l->t('Enter categories separated by comma')); ?></small>
        </div>

        <div class="form-group">
            <label for="budget-notification-threshold"><?php p($l->t('Budget Notification Threshold (%)')); ?></label>
            <input 
                type="number" 
                id="budget-notification-threshold" 
                name="budget_notification_threshold" 
                min="0" 
                max="100" 
                value="<?php p($_['budget_notification_threshold']); ?>"
            >
        </div>

        <div class="form-group">
            <label>
                <input 
                    type="checkbox" 
                    name="financial_goal_tracking" 
                    <?php p($_['financial_goal_tracking'] === 'true' ? 'checked' : ''); ?>
                >
                <?php p($l->t('Enable Financial Goal Tracking')); ?>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="button primary">
                <?php p($l->t('Save Settings')); ?>
            </button>
        </div>
    </form>
</div>
