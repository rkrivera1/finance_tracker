<?php
script('finance_tracker', 'admin');
style('finance_tracker', 'admin');
?>

<div id="finance-tracker-admin" class="section">
    <h2><?php p($l->t('Finance Tracker Settings')); ?></h2>
    
    <div class="settings-group">
        <h3><?php p($l->t('Stock API Configuration')); ?></h3>
        
        <div class="setting-item">
            <label for="alpha-vantage-api-key">
                <?php p($l->t('Alpha Vantage API Key')); ?>
            </label>
            <input type="text" 
                   id="alpha-vantage-api-key" 
                   name="alpha_vantage_api_key"
                   value="<?php p($_['alpha_vantage_api_key']); ?>"
                   placeholder="<?php p($l->t('Enter your Alpha Vantage API key')); ?>"
            >
            <p class="setting-hint">
                <?php p($l->t('Get your API key at')); ?> 
                <a href="https://www.alphavantage.co/support/#api-key" target="_blank" rel="noreferrer noopener">Alpha Vantage</a>
            </p>
        </div>

        <div class="setting-item">
            <label for="stock-update-interval">
                <?php p($l->t('Stock Price Update Interval (minutes)')); ?>
            </label>
            <input type="number" 
                   id="stock-update-interval" 
                   name="stock_update_interval"
                   value="<?php p($_['stock_update_interval'] ?? '5'); ?>"
                   min="1" 
                   max="60"
            >
        </div>
    </div>
</div>
