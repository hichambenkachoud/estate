/*
 * Fuel UX Spinner
 * https://github.com/ExactTarget/fuelux
 *
 * Copyright (c) 2012 ExactTarget
 * Licensed under the MIT license.
 */

// SPINNER CONSTRUCTOR AND PROTOTYPE

var Spinner = function (element, options) {
	this.$element = $(element);
	this.options = $.extend({}, $.fn.spinner.defaults, options);
	this.$input = this.$element.find('.spinner-input');
	this.$element.on('keyup', this.$input, $.proxy(this.change, this));

	if (this.options.hold) {
		this.$element.on('mousedown', '.spinner-up', $.proxy(function() { this.startSpin(true); } , this));
		this.$element.on('mouseup', '.spinner-up, .spinner-down', $.proxy(this.stopSpin, this));
		this.$element.on('mouseout', '.spinner-up, .spinner-down', $.proxy(this.stopSpin, this));
		this.$element.on('mousedown', '.spinner-down', $.proxy(function() {this.startSpin(false);} , this));
	} else {
		this.$element.on('click', '.spinner-up', $.proxy(function() { this.step(true); } , this));
		this.$element.on('click', '.spinner-down', $.proxy(function() { this.step(false); }, this));
	}

	this.switches = {
		count: 1,
		enabled: true
	};

	if (this.options.speed === 'medium') {
		this.switches.speed = 300;
	} else if (this.options.speed === 'fast') {
		this.switches.speed = 100;
	} else {
		this.switches.speed = 500;
	}

	this.lastValue = null;

	this.render();

	if (this.options.disabled) {
		this.disable();
	}
};

Spinner.prototype = {
	constructor: Spinner,

	render: function () {
		var inputValue = this.$input.val();

		if (inputValue) {
			this.value(inputValue);
		} else {
			this.$input.val(this.options.value);
		}

		this.$input.attr('maxlength', (this.options.max + '').split('').length);
	},

	change: function () {
		var newVal = this.$input.val();

		if(newVal/1){
			this.options.value = newVal/1;
		}else{
			newVal = newVal.replace(/[^0-9]/g,'') || '';
			this.$input.val(newVal);
			this.options.value = newVal/1;
		}

		this.triggerChangedEvent();
	},

	stopSpin: function () {
        if(this.switches.timeout!==undefined){
            clearTimeout(this.switches.timeout);
            this.switches.count = 1;
            this.triggerChangedEvent();
        }
	},

	triggerChangedEvent: function () {
		var currentValue = this.value();
		if (currentValue === this.lastValue) return;

		this.lastValue = currentValue;

		// Primary changed event
		this.$element.trigger('changed', currentValue);

		// Undocumented, kept for backward compatibility
		this.$element.trigger('change');
	},

	startSpin: function (type) {

		if (!this.options.disabled) {
			var divisor = this.switches.count;

			if (divisor === 1) {
				this.step(type);
				divisor = 1;
			} else if (divisor < 3){
				divisor = 1.5;
			} else if (divisor < 8){
				divisor = 2.5;
			} else {
				divisor = 4;
			}

			this.switches.timeout = setTimeout($.proxy(function() {this.iterator(type);} ,this),this.switches.speed/divisor);
			this.switches.count++;
		}
	},

	iterator: function (type) {
		this.step(type);
		this.startSpin(type);
	},

	step: function (dir) {
		var curValue = this.options.value;
		var limValue = dir ? this.options.max : this.options.min;
		var digits, multiple;

		if ((dir ? curValue < limValue : curValue > limValue)) {
			var newVal = curValue + (dir ? 1 : -1) * this.options.step;

			if(this.options.step % 1 !== 0){
				digits = (this.options.step + '').split('.')[1].length;
				multiple = Math.pow(10, digits);
				newVal = Math.round(newVal * multiple) / multiple;
			}

			if (dir ? newVal > limValue : newVal < limValue) {
				this.value(limValue);
			} else {
				this.value(newVal);
			}
		} else if (this.options.cycle) {
			var cycleVal = dir ? this.options.min : this.options.max;
			this.value(cycleVal);
		}
	},

	value: function (value) {
		if (!isNaN(parseFloat(value)) && isFinite(value)) {
			value = parseFloat(value);
			this.options.value = value;
			this.$input.val(value);
			return this;
		} else {
			return this.options.value;
		}
	},

	disable: function () {
		this.options.disabled = true;
		this.$input.attr('disabled','');
		this.$element.find('button').addClass('disabled');
	},

	enable: function () {
		this.options.disabled = false;
		this.$input.removeAttr("disabled");
		this.$element.find('button').removeClass('disabled');
	}
};


// SPINNER PLUGIN DEFINITION

$.fn.spinner = function (option) {
	var args = Array.prototype.slice.call( arguments, 1 );
	var methodReturn;

	var $set = this.each(function () {
		var $this   = $( this );
		var data    = $this.data( 'spinner' );
		var options = typeof option === 'object' && option;

		if( !data ) $this.data('spinner', (data = new Spinner( this, options ) ) );
		if( typeof option === 'string' ) methodReturn = data[ option ].apply( data, args );
	});

	return ( methodReturn === undefined ) ? $set : methodReturn;
};

$.fn.spinner.defaults = {
	value: 1,
	min: 1,
	max: 999,
	step: 1,
	hold: true,
	speed: 'medium',
	disabled: false
};

$.fn.spinner.Constructor = Spinner;

$.fn.spinner.noConflict = function () {
	$.fn.spinner = old;
	return this;
};


// SPINNER DATA-API

$(function () {
	$('body').on('mousedown.spinner.data-api', '.spinner', function () {
		var $this = $(this);
		if ($this.data('spinner')) return;
		$this.spinner($this.data());
	});
});



// Spinner
(function(theme, $) {

	theme = theme || {};

	var instanceName = '__spinner';

	var PluginSpinner = function($el, opts) {
		return this.initialize($el, opts);
	};

	PluginSpinner.defaults = {
	};

	PluginSpinner.prototype = {
		initialize: function($el, opts) {
			if ( $el.data( instanceName ) ) {
				return this;
			}

			this.$el = $el;

			this
				.setData()
				.setOptions(opts)
				.build();

			return this;
		},

		setData: function() {
			this.$el.data(instanceName, this);

			return this;
		},

		setOptions: function(opts) {
			this.options = $.extend( true, {}, PluginSpinner.defaults, opts );

			return this;
		},

		build: function() {
			this.$el.spinner( this.options );

			return this;
		}
	};

	// expose to scope
	$.extend(theme, {
		PluginSpinner: PluginSpinner
	});

	// jquery plugin
	$.fn.themePluginSpinner = function(opts) {
		return this.each(function() {
			var $this = $(this);

			if ($this.data(instanceName)) {
				return $this.data(instanceName);
			} else {
				return new PluginSpinner($this, opts);
			}

		});
	}

}).apply(this, [window.theme, jQuery]);

// Spinner
(function($) {

	'use strict';

	if ( $.isFunction($.fn[ 'spinner' ]) ) {

		$(function() {
			$('[data-plugin-spinner]').each(function() {
				var $this = $( this ),
					opts = {};

				var pluginOptions = $this.data('plugin-options');
				if (pluginOptions)
					opts = pluginOptions;

				$this.themePluginSpinner(opts);
			});
		});

	}

}).apply(this, [jQuery]);

// Select2
(function(theme, $) {

	theme = theme || {};

	var instanceName = '__select2';

	var PluginSelect2 = function($el, opts) {
		return this.initialize($el, opts);
	};

	PluginSelect2.defaults = {
		theme: 'bootstrap'
	};

	PluginSelect2.prototype = {
		initialize: function($el, opts) {
			if ( $el.data( instanceName ) ) {
				return this;
			}

			this.$el = $el;

			this
				.setData()
				.setOptions(opts)
				.build();

			return this;
		},

		setData: function() {
			this.$el.data(instanceName, this);

			return this;
		},

		setOptions: function(opts) {
			this.options = $.extend( true, {}, PluginSelect2.defaults, opts );

			return this;
		},

		build: function() {
			this.$el.select2( this.options );

			return this;
		}
	};

	// expose to scope
	$.extend(theme, {
		PluginSelect2: PluginSelect2
	});

	// jquery plugin
	$.fn.themePluginSelect2 = function(opts) {
		return this.each(function() {
			var $this = $(this);

			if ($this.data(instanceName)) {
				return $this.data(instanceName);
			} else {
				return new PluginSelect2($this, opts);
			}

		});
	}

}).apply(this, [window.theme, jQuery]);

// Select2
(function($) {

	'use strict';

	if ( $.isFunction($.fn[ 'select2' ]) ) {

		$(function() {
			$('[data-plugin-selectTwo]').each(function() {
				var $this = $( this ),
					opts = {};

				var pluginOptions = $this.data('plugin-options');
				if (pluginOptions)
					opts = pluginOptions;

				$this.themePluginSelect2(opts);
			});
		});

	}

}).apply(this, [jQuery]);
