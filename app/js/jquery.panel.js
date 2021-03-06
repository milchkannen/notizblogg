/* ===========================================================================
 *
 * @frame: jQuery plugin 'panel' for notizblogg: logo, searchbar & login
 *
 * @author André Kilchenmann code@milchkannen.ch
 *
 * @copyright 2014 by André Kilchenmann (milchkannen.ch)
 *
 * @requires
 *  jQuery - min-version 1.10.2
 *
 * ===========================================================================
 * ======================================================================== */


(function ($) {
	'use strict';
	// -----------------------------------------------------------------------
	// -----------------------------------------------------------------------
	// define some functions first
	// -----------------------------------------------------------------------
	var blur = function(btn, action) {
			var vague = $('.wrapper').Vague({
				intensity:      3,		// Blur Intensity
				forceSVGUrl:    false,	// Force absolute path to the SVG filter,
				// default animation options
			});
			if(action === 'remove' && btn.hasClass('active')) {
				vague.destroy();
			} else {
				// open the form
				vague.blur();
			}
			btn.toggleClass('active');
		},

		radiobutton = function(ele, value, title, btn) {
			var radio;
			var search_field = $('span.search').find('input.search_field');
			var drawer = $('span.search').find('div.drawer');
			ele
			.append(radio = $('<input>')
				.attr({
					'type': 'radio',
					'name': 'filter',
					'value': value
				})
			)
			.append($('<button>')
				.attr({
					'type': 'button',
					'title': title
				})
				.addClass('btn grp_none ' + value)
				.on('click', function() {
					radio.click();
					drawer.slideUp();
					btn.removeClass('fake_btn more label author source all');
					btn.addClass(value);
					search_field.focus();
				})
			);

			radio.change(function() {
				completeMultipleValues(value, search_field, '');
			});
		},

		searchfilter = function(btn) {
		var drawer, filter = {};

		btn.toggleClass('fake_btn more')
			.on('mouseover', function() {
				drawer.css({
					position: 'absolute',
					top: $('header').position().top + $('header').height() +'px',
					left: $(this).position().left
				});
			})
			.on('click', function() {
				if(drawer.is(':visible')) {
					drawer.slideUp();
				} else {
					checkframe();
					drawer.slideDown();
				}
			})
			.after(drawer = $('<div>')
				.addClass('float_obj tiny drawer')
				.append($('<ul>')
					.append(filter.all = $('<li>'))
					.append(filter.label = $('<li>'))
					.append(filter.source = $('<li>'))
					.append(filter.author = $('<li>'))
				)
			);

			radiobutton(filter.all, 'all', 'no filter', btn);
			radiobutton(filter.label, 'label', 'filter label', btn);
			radiobutton(filter.source, 'source', 'filter source', btn);
			radiobutton(filter.author, 'author', 'filter author' , btn);
			$(document).keyup(function(event) {
				if (event.keyCode == 27) {
					if(drawer.is(':visible')) {
						drawer.slideUp();
					}
				}
			});
		},

		checkframe = function() {
			if($('header .btn').hasClass('active')) {
				if($('.float_obj').is(':visible')) {
					$('.float_obj').slideUp();
				}
				$('.btn').removeClass('active');
			}
		};

	// -------------------------------------------------------------------------
	// -------------------------------------------------------------------------
	// define the methods here
	// -------------------------------------------------------------------------
	var methods = {
		/*========================================================================*/
		init: function (options) {
			return this.each(function () {
				var $this = $(this),
					localdata = {};

				localdata.settings = {
					project: 'Notizblogg',	// default: Notizblogg
					logo: 'nb-logo.png',	// default: nb-logo.png
					user: {

					},		// undefined = guest
					action: undefined
				};
				$.extend(localdata.settings, options);
				// initialize a local data object which is attached to the DOM object
				$this.data('localdata', localdata);

			}); // end "return this.each"
		}, // end "init"


		project: function (name, logo) {
			return this.each(function () {
				var $this = $(this);
				var localdata = $this.data('localdata');
				var project;
				$this
				.append($('<a>')
					.attr({
						href: NB.url
					})
					.append($('<img>')
						.attr({
							src: NB.media + '/project/' + logo
						})
						.addClass('title project logo')
					)
					.append($('<h2>')
						.text(name)
						.addClass('title project name')
					)
				);

			});
		},

		search: function (action) {
			return this.each(function () {
				var $this = $(this);
				var localdata = $this.data('localdata');
				var search = {};
				$this.append(search.form = $('<form>')
					.attr({
						'accept-charset': 'utf-8',
						'name': 'simpleSearch',
						'action': action,
						'method': 'get'
					})
					.append(search.filter = $('<button>')
						.attr({
							'type': 'button',
						})
						.addClass('btn grp_left fake_btn filter')
					)
					.append(search.field = $('<input>')
						.attr({
							'type': 'search',
							'title': 'SEARCH',
							'placeholder': 'search',
							'name': 'q',
							'accept-charset': 'utf-8'
						})
						.addClass('input grp_middle search_field')
					)
					.append(search.button = $('<button>')
						.attr({
							'type': 'submit',
							'title': 'GO!'
						})
						.addClass('btn grp_right search')
					)
				);
				searchfilter(search.filter);
				search.field.focus(function () {
					search.field
						.attr({
							'placeholder': ''
						})
						.css({
							'background-color': '#ffffe0'
						});
						$(this).select();
					search.filter.css({
						'background-color': '#ffffe0'
					});
					search.button.css({
						'background-color': '#ffffe0'
					});
				});
				search.field.focusout(function () {
					search.field
						.attr({
							'placeholder': 'search'
						})
						.css({
							'background-color': '#ffffff'
						});
					search.filter.css({
						'background-color': '#ffffff'
					});
					search.button.css({
						'background-color': '#ffffff'
					});
				});
			});
		},

		login: function (action) {
			return this.each(function () {
				var $this = $(this);
				var localdata = $this.data('localdata');
				var login = {};

				$this.append(
					login.user = $('<button>')
					.addClass('btn grp_none user')
				);

				$this.append(
					login.frame = $('<div>')
						.addClass('float_obj medium login_frame')
						.append(
							login.form = $('<form>')
							.attr({
								'action': action,
								'method': 'post'
							})
							.append($('<p>')
								.append(login.name = $('<input>')
									.attr({
										'type': 'text',
										'name': 'usr',
										'title': 'Username',
										'placeholder': 'Username'
									})
								.addClass('field_obj small')
								)
							)

							.append($('<p>')
								.append($('<input>')
									.attr({
										'type': 'password',
										'name': 'key',
										'title': 'Password',
										'placeholder': 'Password'
									})
									.addClass('field_obj small')
								)
							)
							.append($('<p>')
								.append($('<input>')
									.attr({
										'type': 'hidden',
										'name': 'uri',
										'value': NB.uri
									})
									.addClass('field_obj small')
								)
							)
							.append($('<p>')
								.append($('<input>')
									.attr({
										'type': 'submit',
										'title': 'Login',
										'value': 'Login'
									}).text('Login')
									.addClass('button small submit')
								)
							)
						)
				);
				// set position of float_obj
				login.user
					.on('mouseover', function() {
						login.frame.css({
							position: 'absolute',
							top: $('header').position().top + $('header').height() +'px',
							left: $(this).position().left - login.frame.width() + 'px'
						});
					})
					.on('click', function() {
						if(login.frame.is(':visible')) {
							login.frame.slideUp();
							blur(login.user, 'remove');
						} else {
							checkframe();
							blur(login.user);
							login.frame.slideDown();
							login.name.focus();
						}
				});
				$(document).keyup(function(event) {
					if (event.keyCode == 27) {
						if(login.frame.is(':visible')) {
							login.frame.slideUp();
							blur(login.user, 'remove');
						}
					}
				});
			});
		},
		log: function (action) {
			return this.each(function () {
				var $this = $(this);
				var localdata = $this.data('localdata');
				var log = {};

				$this.append(
						log.user = $('<button>')
						.addClass('btn grp_none user img')
						.css({
							'background-image': 'url("' + NB.user.avatar + '")',
							'background-repeat': 'no-repeat',
							'background-position': 'center',
							'background-size': '40px',
							'border': 'none'
						})
					);

				$this.append(
					log.frame = $('<div>')
						.addClass('float_obj medium logout_frame')
						.append($('<h3>').text('Export').css({'text-align': 'center'}))
						.append($('<p>')
							.append($('<a>')
								.attr({
									'href': 'bibtex.php'
								})
								.append($('<input>')
									.attr({
										'type': 'submit',
										'title': 'bibTex',
										'value': 'bibTex'
									}).text('bibTex')
									.addClass('button small submit')
								)
							)
						)
						.append($('<hr>'))
						.append(
							log.form = $('<form>')
							.attr({
								'action': action,
								'method': 'post'
							})
							.append($('<p>')
								.append($('<input>')
									.attr({
										'type': 'submit',
										'title': 'Logout',
										'value': 'Logout'
									}).text('Logout')
									.addClass('button small reset')
								)
								.css({
									float: 'right'
								})
							)
						)
				);
				// set position of float_obj
				log.user
					.on('mouseover', function() {
						log.frame.css({
							position: 'absolute',
							top: $('header').position().top + $('header').height() +'px',
							left: $(this).position().left - log.frame.width() + 'px'
						});
					})
					.on('click', function() {
						if(log.frame.is(':visible')) {
							log.frame.slideUp();
							blur(log.user, 'remove');
						} else {
							checkframe();
							blur(log.user);
							log.frame.slideDown();
						}
					});
				$(document).keyup(function(event) {
					if (event.keyCode == 27) {
						if(log.frame.is(':visible')) {
							log.frame.slideUp();
							blur(log.user, 'remove');
						}
					}
				});
			});
		},



		add: function () {
			return this.each(function () {
				var $this = $(this);
				var localdata = $this.data('localdata');
				var form = {};
				$('header').after(
					form.frame = $('<div>')
						.addClass('float_obj large form_frame')
						.form('add', NB.api + '/post.php')
				);
				$this.append(
					form.new = $('<button>')
						.attr({
							'type': 'button',
							'title': 'add new'
						})
						.addClass('btn grp_none plus')

						.on('mouseover', function() {
							form.frame.css({
								position: 'absolute',
								top: $('header').position().top + $('header').height() +'px'
							});
						})
						.on('click', function(){
							if(form.frame.is(':visible')) {
								form.frame.slideUp();
								blur(form.new, 'remove');
							} else {
								checkframe();
								blur(form.new);
								form.frame.slideDown();
								$('.first_form_ele').focus();
							}
						})
					);
					$(document).keyup(function(event) {
						if (event.keyCode == 27) {
							if(form.frame.is(':visible')) {
								form.frame.slideUp();
								blur(form.new, 'remove');
							}
						}
					});
				});
			},

		foot:function (version) {
			return this.each(function () {
				var $this = $(this);
				var localdata = $this.data('localdata');
				var curDate = new Date(),
					curYear = curDate.getFullYear();
				$this.append(
					$('<p>').addClass('small')
					.append(
						$('<span>').addClass('project')
						.append($('<a>')
							.attr({
								'href': 'http://notizblogg.ch'
							})
							.html('Notizblogg')
						)
						.append(' (' + version + ')')
					)
					.append(
						$('<span>').addClass('definition').html(' | ')
						.append($('<a>')
							.attr({
								'href': 'http://notizblogg.ch'
							})
							.html('Idea, Concept and Design')
						)
					)
					.append(
						$('<span>').addClass('copyright').html(' &copy; ')
						.append($('<a>')
							.attr({
								'href': 'https://plus.google.com/u/0/102518416171514295136/posts?rel=author'
							})
							.html('André Kilchenmann')
						)
					)
					.append(
						$('<span>').addClass('year').text(' | 2006-' + curYear)
					)
					.append(
						$('<span>').addClass('partner')
						.append($('<a>')
							.attr({
								'href': 'http://milchkannen.ch'
							})
							.append(
								$('<img>').attr({
									'src': 'app/style/img/akM-logo-small.png',
									'alt': 'milchkannen | kilchenmann',
									'title': 'milchkannen | andré kilchenmann'
								})

							)
						)
					)
				);
			});
		},


		anotherMethod: function () {
				return this.each(function () {
					var $this = $(this);
					var localdata = $this.data('localdata');
				});
			}
			/*========================================================================*/
	};



	$.fn.panel = function (method) {
		// Method calling logic
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			throw 'Method ' + method + ' does not exist on jQuery.tooltip';
		}
	};
})(jQuery);
