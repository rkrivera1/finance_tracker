<?php
script('finance_tracker', 'script');
style('finance_tracker', 'style');
?>

<div id="app-navigation">
	<ul>
		<li>
			<a href="#" class="icon-home svg" data-section="dashboard">
				<?php p($l->t('Dashboard')); ?>
			</a>
		</li>
		<li>
			<a href="#" class="icon-history svg" data-section="transactions">
				<?php p($l->t('Transactions')); ?>
			</a>
		</li>
		<li>
			<a href="#" class="icon-category-office svg" data-section="investments">
				<?php p($l->t('Investments')); ?>
			</a>
		</li>
		<li>
			<a href="#" class="icon-category-monitoring svg" data-section="budget">
				<?php p($l->t('Budget')); ?>
			</a>
		</li>
	</ul>
	
	<div id="app-settings">
		<div id="app-settings-header">
			<button class="settings-button" data-apps-slide-toggle="#app-settings-content">
				<?php p($l->t('Settings')); ?>
			</button>
		</div>
		<div id="app-settings-content" class="hidden">
			<div class="settings-section">
				<h3><?php p($l->t('API Configuration')); ?></h3>
				<form id="api-settings-form">
					<p>
						<label for="stock-api-key">
							<?php p($l->t('Stock API Key')); ?>
						</label>
						<input type="password" id="stock-api-key" name="stock-api-key" />
					</p>
					<p>
						<label for="stock-api-provider">
							<?php p($l->t('API Provider')); ?>
						</label>
						<select id="stock-api-provider" name="stock-api-provider">
							<option value="alphavantage">Alpha Vantage</option>
							<option value="finnhub">Finnhub</option>
						</select>
					</p>
					<input type="submit" value="<?php p($l->t('Save')); ?>" />
				</form>
			</div>
		</div>
	</div>
</div>

<div id="app-content">
	<div id="app-content-wrapper">
		<!-- Empty content message -->
		<div id="emptycontent" class="hidden">
			<div class="icon-folder"></div>
			<h2><?php p($l->t('No data available')); ?></h2>
			<p><?php p($l->t('Start by adding your first transaction or budget.')); ?></p>
		</div>

		<!-- Loading spinner -->
		<div id="loading" class="icon-loading"></div>

		<!-- Content sections -->
		<div id="content-view"></div>
	</div>
</div>