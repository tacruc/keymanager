<table>
	<thead>
		<tr>
			<th><?php p($l->t("Identity")) ?></th>
			<th><?php p($l->t("valid from")) ?></th>
			<th><?php p($l->t("valid until")) ?></th>
			<!--<th><?php/* p($l->t("detail")) FIXME when detail is avalible in phpgpg*/?></th>-->
			<th><?php p($l->t("finterprint")) ?></th>
		</tr>
	</thead>
	<tbody id="keys-table">
		<script id="keys-tpl" type="text/x-handlebars-template">
		{{#each keys}}
			<tr class="app-keymanager-keyentry" ng-class="{'private':private">
				<td>
					<div class="app-keymanager-identity-item">
						<img class="app-keymanager-identity-item-icon contact__icon" ng-show="identities.[0].photo()!==undefined">
						<div class="app-keymanager-identity-item-icon contact__icon" ng-show="identities.[0].photo()===undefined" ng-style="{'background-color': (identities.[0].uid) }">{{identities.[0].name}}</div>
						<div class="app-content-list-item-line-one" ng-class="{'no-line-two':!identities.[0].email}">{{identities.[0].name}}</div>
						<div class="app-content-list-item-line-two">{{identities.[0].email}}</div>

					</div>
				</td>
				<td>
					{{valid}}
				</td>
				<td>
					{{expires}}
				</td>
				{{!<td>
					{{detail}}
				</td>}}
				<td>
					{{fingerprint}}
				</td>
			</tr>
		{{/each}}
		</script>
	</tbody>
</table>