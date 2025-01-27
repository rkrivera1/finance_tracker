<?php
script('finance_tracker', 'settings');
style('finance_tracker', 'settings');

/** @var \OCP\IL10N $l */
/** @var array $_ */
?>

<div class="section" id="finance-tracker-admin-settings">
    <h2><?php p($l->t('Finance Tracker Settings')); ?></h2>

    <form id="finance-tracker-admin-form">
        <div class="section-block">
            <h3><?php p($l->t('Global Configuration')); ?></h3>
            
            <div class="settings-input">
                <label for="default-currency"><?php p($l->t('Default Currency')); ?></label>
                <select id="default-currency" name="default_currency">
                    <option value="USD" <?php p($_['currency_default'] === 'USD' ? 'selected' : ''); ?>>USD</option>
                    <option value="EUR" <?php p($_['currency_default'] === 'EUR' ? 'selected' : ''); ?>>EUR</option>
                    <option value="GBP" <?php p($_['currency_default'] === 'GBP' ? 'selected' : ''); ?>>GBP</option>
                    <option value="JPY" <?php p($_['currency_default'] === 'JPY' ? 'selected' : ''); ?>>JPY</option>
                </select>
            </div>

            <div class="settings-input">
                <label for="data-retention"><?php p($l->t('Data Retention Period (Days)')); ?></label>
                <input type="number" 
                       id="data-retention" 
                       name="data_retention_period" 
                       min="30" 
                       max="365" 
                       value="<?php p($_['data_retention_period']); ?>"
                >
                <p class="settings-hint">
                    <?php p($l->t('How long financial data will be stored before archiving')); ?>
                </p>
            </div>
        </div>

        <div class="section-block">
            <h3><?php p($l->t('Security & Privacy')); ?></h3>
            
            <div class="settings-input">
                <input type="checkbox" 
                       id="anonymize-data" 
                       name="anonymize_data"
                       <?php p($_['anonymize_data'] ? 'checked' : ''); ?>
                >
                <label for="anonymize-data"><?php p($l->t('Anonymize Aggregated Data')); ?></label>
                <p class="settings-hint">
                    <?php p($l->t('Remove personal identifiers from aggregated financial reports')); ?>
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
