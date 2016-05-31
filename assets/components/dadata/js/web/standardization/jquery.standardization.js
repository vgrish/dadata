(function() {
	'use strict';

	$.fn.standardization = function(options, args) {

		var settings = $.extend({
			eventNS: '.standardization',
			return: {}
		}, options);

		var types = {};

		var utils = (function () {
			return {
				compact: function (array) {
					return $.grep(array, function (el) {
						return !!el;
					});
				}
			};
		}());

		types['address'] = {
			value: function (data, settings) {
				var params = settings.params,
					fields = ['result'],
					result = [];

				if (data && data.return !== null && data.return !== undefined) {
					return data.return;
				}

				if (params && params.parts) {
					fields = $.map(params.parts, function (part) {
						return part.toLowerCase();
					});
				}

				$.each(fields, function (i, field) {
					if (!!(data[field])) {
						result.push(data[field]);
					}
				});

				return utils.compact(result).join(' ');
			}
		};

		types['phone'] = {
			value: function (data, settings) {
				var params = settings.params,
					fields = ['phone'],
					result = [];

				if (data && data.return !== null && data.return !== undefined) {
					return data.return;
				}

				if (params && params.parts) {
					fields = $.map(params.parts, function (part) {
						return part.toLowerCase();
					});
				}

				$.each(fields, function (i, field) {
					if (!!(data[field])) {
						result.push(data[field]);
					}
				});

				return utils.compact(result).join(' ');
			}
		};

		types['passport'] = {
			value: function (data, settings) {
				var params = settings.params,
					fields = ['series', 'number'],
					result = [];

				if (data && data.return !== null && data.return !== undefined) {
					return data.return;
				}

				if (params && params.parts) {
					fields = $.map(params.parts, function (part) {
						return part.toLowerCase();
					});
				}

				$.each(fields, function (i, field) {
					if (!!(data[field])) {
						result.push(data[field]);
					}
				});

				return utils.compact(result).join(' ');
			}
		};

		types['name'] = {
			value: function (data, settings) {
				var params = settings.params,
					fields = ['result'],
					result = [];

				if (data && data.return !== null && data.return !== undefined) {
					return data.return;
				}

				if (params && params.parts) {
					fields = $.map(params.parts, function (part) {
						return part.toLowerCase();
					});
				}

				$.each(fields, function (i, field) {
					if (!!(data[field])) {
						result.push(data[field]);
					}
				});

				return utils.compact(result).join(' ');
			}
		};

		types['email'] = {
			value: function (data, settings) {
				var params = settings.params,
					fields = ['email'],
					result = [];

				if (data && data.return !== null && data.return !== undefined) {
					return data.return;
				}

				if (params && params.parts) {
					fields = $.map(params.parts, function (part) {
						return part.toLowerCase();
					});
				}

				$.each(fields, function (i, field) {
					if (!!(data[field])) {
						result.push(data[field]);
					}
				});

				return utils.compact(result).join(' ');
			}
		};

		types['birthdate'] = {
			value: function (data, settings) {
				var params = settings.params,
					fields = ['birthdate'],
					result = [];

				if (data && data.return !== null && data.return !== undefined) {
					return data.return;
				}

				if (params && params.parts) {
					fields = $.map(params.parts, function (part) {
						return part.toLowerCase();
					});
				}

				$.each(fields, function (i, field) {
					if (!!(data[field])) {
						result.push(data[field]);
					}
				});

				return utils.compact(result).join(' ');
			}
		};

		types['vehicle'] = {
			value: function (data, settings) {
				var params = settings.params,
					fields = ['result'],
					result = [];

				if (data && data.return !== null && data.return !== undefined) {
					return data.return;
				}

				if (params && params.parts) {
					fields = $.map(params.parts, function (part) {
						return part.toLowerCase();
					});
				}

				$.each(fields, function (i, field) {
					if (!!(data[field])) {
						result.push(data[field]);
					}
				});

				return utils.compact(result).join(' ');
			}
		};

		return this.each(function(e) {
			var self = this;
			self.$el = $(this);
			self.$parent = self.$el.parent();
			self.type = options.type;

			var clean = function(request) {
				return $.ajax({
					type: 'POST',
					url: settings.serviceUrl + '/clean/' + settings.type,
					headers: {
						'Authorization': 'Token ' + settings.token
					},
					contentType: 'application/json',
					dataType: 'json',
					cache: false,
					data: JSON.stringify({
						query: self.$el.val(),
						return: settings.return
					})
				});
			};

			/**
			 * @constructor
			 */
			self.init = function() {
				self.$el.change(function() {
					if (!self.$el.val()) {
						self.clearState();
					} else {
						clean({
							structure: [self.type],
							data: [
								[self.$el.val()]
							]
						})
							.done(function(response) {
								self.validate(response.standard);
							});
					}
				});
			};

			/**
			 * Проводит валидацию поля, основываясь на стандартизованном ответе от Dadata
			 * @param validated Ответ от Dadata
			 */
			self.validate = function(validated) {
				/* если код качества 'Корректный', показываем статус ОК */
				if (validated.qc == 0) {
					self.$el.val(
						self.getValue(validated)
					).trigger('DaData_onStandard', [validated, settings, self.$el]);
					self.clearState();
					self.setOK();
				} else {
					self.setError();
				}
			};

			/**
			 * Устанавливает статус Ошибка
			 */
			self.setError = function() {
				self.$parent.addClass('has-error has-feedback');
				self.$parent.find('span').remove();
				self.$parent.append('<span class=\'glyphicon glyphicon-remove form-control-feedback\'></span>')
			};

			/**
			 * Устанавливает статус ОК
			 */
			self.setOK = function() {
				self.$parent.addClass('has-success has-feedback');
				self.$parent.append('<span class=\'glyphicon glyphicon-ok form-control-feedback\'></span>')
			};

			/**
			 * Снимает статус
			 */
			self.clearState = function() {
				self.$parent.removeClass('has-error has-success has-feedback');
				self.$parent.find('span').remove();
			};

			self.getValue = function(validated) {
				return types[settings.type].value(validated, settings);
			};

			self.init();
		});

	};

})();
