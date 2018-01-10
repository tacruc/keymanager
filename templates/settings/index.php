<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button"
				data-apps-slide-toggle="#app-settings-content"
		><?php p($l->t('Settings'));?></button>
	</div>
	<div id="app-settings-content">
		<input type="checkbox" id="hide-revoked" class="checkbox"
			   checked="checked">
		<label for="hide-revoked"><?php p($l->t("Hide revoked Keys"))?></label><br>
	</div>
</div>
