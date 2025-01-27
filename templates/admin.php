<?php
script('finance_tracker', 'admin');
style('finance_tracker', 'admin');
?>

<div id="finance-tracker-admin" class="section">
    <h2><?php p($l->t('Finance Tracker Settings')); ?></h2>
    
    <form id="finance-tracker-admin-form" class="finance-tracker-admin-settings">
        <div class="form-group">
            <label for="stock-api-key"><?php p($l->t('Default Stock API Key')); ?></label>
            <input type="password" 
                   name="stock-api-key" 
                   id="stock-api-key" 
                   class="form-control"
                   placeholder="<?php p($l->t('Enter API key')); ?>"
                   value="<?php p($_['stockApiKey'] ?? ''); ?>">
            <p class="help-block">
                <?php p($l->t('Enter a default API key for stock market data. Users can override this in their personal settings.')); ?>
            </p>
        </div>

        <div class="form-group">
            <label for="stock-api-provider"><?php p($l->t('Stock API Provider')); ?></label>
            <select name="stock-api-provider" 
                    id="stock-api-provider" 
                    class="form-control">
                <option value="alphavantage" <?php p($_['stockApiProvider'] === 'alphavantage' ? 'selected' : ''); ?>>
                    Alpha Vantage
                </option>
                <option value="finnhub" <?php p($_['stockApiProvider'] === 'finnhub' ? 'selected' : ''); ?>>
                    Finnhub
                </option>
                <option value="iex" <?php p($_['stockApiProvider'] === 'iex' ? 'selected' : ''); ?>>
                    IEX Cloud
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="update-interval"><?php p($l->t('Default Update Interval')); ?></label>
            <select name="update-interval" 
                    id="update-interval" 
                    class="form-control">
                <option value="300" <?php p($_['updateInterval'] === '300' ? 'selected' : ''); ?>>
                    <?php p($l->t('5 minutes')); ?>
                </option>
                <option value="600" <?php p($_['updateInterval'] === '600' ? 'selected' : ''); ?>>
                    <?php p($l->t('10 minutes')); ?>
                </option>
                <option value="900" <?php p($_['updateInterval'] === '900' ? 'selected' : ''); ?>>
                    <?php p($l->t('15 minutes')); ?>
                </option>
                <option value="1800" <?php p($_['updateInterval'] === '1800' ? 'selected' : ''); ?>>
                    <?php p($l->t('30 minutes')); ?>
                </option>
            </select>
        </div>

        <div class="form-group">
            <input type="submit" 
                   class="button primary" 
                   value="<?php p($l->t('Save')); ?>">
        </div>
    </form>
</div>