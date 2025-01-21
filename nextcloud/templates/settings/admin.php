<?php
/** @var array $_ */
/** @var \OCP\IL10N $l */
script('finance_tracker', 'admin-settings');
style('finance_tracker', 'admin-settings');
?>

<div class="section" id="finance-tracker-admin-settings">
    <h2><?php p($l->t('Finance Tracker Admin Settings')); ?></h2>

    <form id="finance-tracker-admin-form">
        <div class="form-group">
            <label for="global-currency"><?php p($l->t('Global Default Currency')); ?></label>
            <select id="global-currency" name="global_currency">
                <option value="USD" <?php p($_['global_currency'] === 'USD' ? 'selected' : ''); ?>>USD</option>
                <option value="EUR" <?php p($_['global_currency'] === 'EUR' ? 'selected' : ''); ?>>EUR</option>
                <option value="GBP" <?php p($_['global_currency'] === 'GBP' ? 'selected' : ''); ?>>GBP</option>
                <!-- Add more currency options -->
            </select>
        </div>

        <div class="form-group">
            <label for="data-retention-period"><?php p($l->t('Data Retention Period (Days)')); ?></label>
            <input 
                type="number" 
                id="data-retention-period" 
                name="data_retention_period" 
                min="30" 
                max="3650" 
                value="<?php p($_['data_retention_period']); ?>"
            >
        </div>

        <div class="form-group">
            <label>
                <input 
                    type="checkbox" 
                    name="enable_multi_currency" 
                    <?php p($_['enable_multi_currency'] === 'true' ? 'checked' : ''); ?>
                >
                <?php p($l->t('Enable Multi-Currency Support')); ?>
            </label>
        </div>

        <div class="form-group">
            <label for="default-budget-categories"><?php p($l->t('Default Budget Categories')); ?></label>
            <textarea id="default-budget-categories" name="default_budget_categories" rows="6"><?php 
                p(json_encode(
                    json_decode($_['default_budget_categories'], true), 
                    JSON_PRETTY_PRINT
                )); 
            ?></textarea>
            <small><?php p($l->t('Enter categories separated by comma')); ?></small>
        </div>

        <div class="form-group">
            <label>
                <input 
                    type="checkbox" 
                    name="enable_anonymous_stats" 
                    <?php p($_['enable_anonymous_stats'] === 'true' ? 'checked' : ''); ?>
                >
                <?php p($l->t('Enable Anonymous Usage Statistics')); ?>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="button primary">
                <?php p($l->t('Save Settings')); ?>
            </button>
        </div>
    </form>
</div>
