<?php
script('finance_tracker', 'admin-settings');
style('finance_tracker', 'admin-settings');
?>

<div class="section" id="finance-tracker-stock-settings">
    <h2><?php p($l->t('Stock Price API Settings')); ?></h2>
    
    <form id="finance-tracker-stock-api-form">
        <div class="stock-api-providers">
            <div class="stock-api-provider">
                <h3>Alpha Vantage</h3>
                <label for="alpha_vantage_api_key">
                    <?php p($l->t('Alpha Vantage API Key')); ?>
                </label>
                <input 
                    type="text" 
                    id="alpha_vantage_api_key" 
                    name="alpha_vantage_api_key" 
                    value="<?php p($_['alpha_vantage_api_key']); ?>" 
                    placeholder="<?php p($l->t('Enter Alpha Vantage API Key')); ?>"
                >
                <p class="description">
                    <?php p($l->t('Get your free API key at alphavantage.co')); ?>
                </p>
            </div>

            <div class="stock-api-provider">
                <h3>Finnhub</h3>
                <label for="finnhub_api_key">
                    <?php p($l->t('Finnhub API Key')); ?>
                </label>
                <input 
                    type="text" 
                    id="finnhub_api_key" 
                    name="finnhub_api_key" 
                    value="<?php p($_['finnhub_api_key']); ?>" 
                    placeholder="<?php p($l->t('Enter Finnhub API Key')); ?>"
                >
                <p class="description">
                    <?php p($l->t('Get your free API key at finnhub.io')); ?>
                </p>
            </div>

            <div class="stock-api-provider">
                <h3>Twelve Data</h3>
                <label for="twelve_data_api_key">
                    <?php p($l->t('Twelve Data API Key')); ?>
                </label>
                <input 
                    type="text" 
                    id="twelve_data_api_key" 
                    name="twelve_data_api_key" 
                    value="<?php p($_['twelve_data_api_key']); ?>" 
                    placeholder="<?php p($l->t('Enter Twelve Data API Key')); ?>"
                >
                <p class="description">
                    <?php p($l->t('Get your free API key at twelvedata.com')); ?>
                </p>
            </div>
        </div>

        <div class="stock-tracking-settings">
            <h3><?php p($l->t('Stock Tracking Settings')); ?></h3>
            
            <div class="setting-row">
                <label for="stock_update_frequency">
                    <?php p($l->t('Update Frequency (minutes)')); ?>
                </label>
                <input 
                    type="number" 
                    id="stock_update_frequency" 
                    name="stock_update_frequency" 
                    value="<?php p($_['stock_update_frequency']); ?>" 
                    min="1" 
                    max="60"
                >
            </div>

            <div class="setting-row">
                <label for="stock_tracking_enabled">
                    <?php p($l->t('Enable Stock Tracking')); ?>
                </label>
                <input 
                    type="checkbox" 
                    id="stock_tracking_enabled" 
                    name="stock_tracking_enabled" 
                    <?php p($_['stock_tracking_enabled'] === 'true' ? 'checked' : ''); ?>
                >
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="primary">
                <?php p($l->t('Save Stock API Settings')); ?>
            </button>
        </div>
    </form>
</div>
