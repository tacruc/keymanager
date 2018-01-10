(function (OC, window, $, undefined) {
	'use strict';

	$(document).ready(function () {


// this Keys object holds all our Keys
		var Keys = function (baseUrl) {
			this._baseUrl = baseUrl;
			this._keys = [];
			this._activeKey = undefined;
		};

		Keys.prototype = {
			load: function (id) {
				var self = this;
				this._keys.forEach(function (key) {
					if (key.id === id) {
						key.active = true;
						self._activeKey = key;
					} else {
						key.active = false;
					}
				});
			},
			getActive: function () {
				return this._activeKey;
			},
			getAll: function () {
				return this._keys;
			},
			loadAll: function () {
				var deferred = $.Deferred();
				var self = this;
				$.get(this._baseUrl).done(function (keys) {
					self._activeKey = undefined;
					self._keys = keys;
					deferred.resolve();
				}).fail(function () {
					deferred.reject();
				});
				return deferred.promise();
			}
		};

// this will be the view that is used to update the html
		var View = function (keys) {
			this._keys = keys;
		};

		View.prototype = {
			renderKeyList: function () {
				var source = $('#keys-tpl').html();
				var template = Handlebars.compile(source);
				var html = template({keys: this._keys.getAll()});

				$('#keys-table').html(html);

			},

			render: function () {
				this.renderKeyList();
			}
		};

		var keys = new Keys(OC.generateUrl('/apps/keymanager/keys'));
		var view = new View(keys);
		keys.loadAll().done(function () {
			view.render();
		}).fail(function () {
			alert('Could not load keys');
		});

	});

})(OC, window, jQuery);