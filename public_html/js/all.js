popslide =  '';

//window.addEventListener("orientationchange", function(event){
    //alert(JSON.stringify(screen.orientation));
	//alert(123);
    //window.orientation.lock('portrait');
    /*
    switch(window.orientation)
    {
        case -90: case 90:


        break;
        default:
		// Device is in portrait mode
    }*/
//});

jQuery(document).ready(function($){

	$('.login-btn.btn-second #login').click(function(e){
		$('.lfield').css('display', 'inline-block');
		$('.login-btn.btn-second').hide();
		$('.login-btn.btn-first').css('display', 'inline-block');
		console.log('test');
		return false;
	});
	
	
	$('.ui.progress').hide();

	/*	$('.hslider').owlCarousel({
	 items:1,
	 loop: true,
	 dots: false,
	 nav: false,
	 margin:0,
	 lazyLoad:true,
	 autoplay:true,
	 smartSpeed: 1000,
	 rtl:true,
	 animateOut: 'fadeOut'
	 });*/
	$('.hslider').lightSlider({
		gallery:false,
		item:1,
		freeMove:false,
		thumbItem:0,
		slideMargin: 0,
		speed: 3000,
		pause: 9000,
		mode: 'fade',
		auto: true,
		loop: true,
		pager: false,
		enableDrag: true,
		enableTouch: true,
		swipeThreshold: 40,
		onSliderLoad: function() {
			$('.imageslide').removeClass('cS-hidden');
		}

	});

	$(".banner-app-download .close").on('click',function(){
		$.ajax({
			url: '/close_app_notification'
		})
		$('.banner-app-download').hide();
	});
	
	//DROPDOWN
	$(".custom-select").each(function(){
		$(this).wrap("<span class='select-wrapper'></span>");
		$(this).after("<span class='holder'></span>");
	});
	$(".custom-select").change(function(){
		var selectedOption = $(this).find(":selected").text();
		$(this).next(".holder").text(selectedOption);
	}).trigger('change');

	// Top menu JS	
	$(".tglmenu").click(function(){
		//$('.inner_menu ul').slideToggle(100);
	});
	$(window).resize(function(){
		if($(window).width() > 800 && $('.inner_menu ul').is(':hidden')) {
			$('.inner_menu ul').removeAttr('style');
		}
	});
	// Mid title
	$(".mhover").click(function(){
		$('.msearch_div').slideToggle(150);
		$(this).toggleClass('active');
	});

	//GO TOP
	$('.go-top').click(function () {
		$('html, body').animate({
			scrollTop:0
		}, 'showscroll');
		return false;
	});


	$(window).scroll(function () {
		if ($(this).scrollTop() >= 200) {
			$('.go-top').addClass("showscroll");
		}else {
			$('.go-top').removeClass("showscroll");
		}

		$('.header').toggleClass('sticky', $(document).scrollTop() >= 100);
	});

	$(document).scroll(function() {
		var y = $(this).scrollTop();
		if (y > 100) {
			$('.go-top').fadeIn();
		} else {
			$('.go-top').fadeOut();
		}
	});

	//var popslide = '';
	//== Popup
	/*if($('.popup.pgal_popup1').size() == 1) {
		$('#templates').prev('.popup').remove();
		var popslide = $('.popup.pgal_popup1 .popslider').lightSlider({
			gallery: false,
			item: 1,
			freeMove: false,
			thumbItem: 0,
			slideMargin: 0,
			speed: 3000,
			pause: 9000,
			mode: 'fade',
			auto: true,
			loop: true,
			pager: false,
			enableDrag: true,
			enableTouch: true,
			swipeThreshold: 40,
			onSliderLoad: function () {
				$('.imageslide').removeClass('cS-hidden');
			}
		});
	}*/
	var popupSlider = $('.popup.pgal_popup .popslider').lightSlider({
		gallery: false,
		item: 1,
		freeMove: false,
		thumbItem: 0,
		slideMargin: 0,
		speed: 3000,
		pause: 9000,
		mode: 'fade',
		auto: true,
		loop: true,
		pager: false,
		enableDrag: true,
		enableTouch: true,
		swipeThreshold: 40,
		onSliderLoad: function () {
			$('.imageslide').removeClass('cS-hidden');
		}
	});

	$('.popup.pgal_popup1 .popslider').lightSlider({
		gallery: false,
		item: 1,
		freeMove: false,
		thumbItem: 0,
		slideMargin: 0,
		speed: 3000,
		pause: 9000,
		mode: 'fade',
		auto: true,
		loop: true,
		pager: false,
		enableDrag: true,
		enableTouch: true,
		swipeThreshold: 40,
		onSliderLoad: function () {
			$('.imageslide').removeClass('cS-hidden');
		}
	});

	$('.profile_slider').lightSlider({
		gallery:false,
		item:1,
		freeMove:false,
		thumbItem:0,
		slideMargin: 0,
		speed: 2000,
		pause: 9000,
		//mode: 'fade',
		auto: false,
		//rtl:true,
		loop: true,
		pager: true,
		enableDrag: true,
		enableTouch: true,
		swipeThreshold: 40,
		onSliderLoad: function() {
			$('.imageslide').removeClass('cS-hidden');
		}

	});

	$('.close').click(function() {
		$('.popup').fadeOut(200);
		$('body').removeClass('ovh');
		$('body').removeClass('ovh1');
		$('body').removeClass('ovh2');
		if(typeof popslide.destroy === 'function') {
			popslide.destroy();
		}
	});
	/*
	$(".pbutton").click(function(e) {
		e.preventDefault();
		$(this).next('.popup').fadeIn(200);
		popslide.resize();
		$('body').addClass('ovh');
	});
	$(".pgal").click(function(e) {
		e.preventDefault();
		$('.popup').fadeIn(200);
		popslide.resize();
		$('body').addClass('ovh1');
	});
	*/
	$(".psimg a, .pgal").click(function(e) {
		e.preventDefault();

		var className = '.pgal_popup';
		if(!$(this).hasClass('pgal')){
			className += '1';
			$('body').addClass('ovh2');
		}else{
			var slide = 1;
			if($(this).parents('.profile-top').size() == 1){
				slide = 1
			}else{
				slide = $(this).parents('li').index() + 1;
			}

			popupSlider.goToSlide(slide);
			//$(className + ' .popslider').goToSlide(1);
			//$(className + ' .popslider .lslide').removeClass('active').eq(1).addClass('active');
			$('body').addClass('ovh1');
		}
		$(className).fadeIn(200);
		$(className + ' .popslider').css({'height':(($(window).height()))+'px'});
		//popslider
		//popslide.resize();

	});


	var yy = $(window).height();
	//var ww = $(window).width();
	$('.pgalslid1').css({'height': yy});
	$(window).resize(function(){ // On resize
		$('.pgalslid1').css({'height':(($(window).height()))+'px'});
	});

	/*
	//== chkbox
	$('.chkbox').click(function () {
		$(this).addClass("active");
		$(this).parent().addClass("active");
		if ($(this).find('input:checkbox').is(":checked")) {
			$(this).find('input:checkbox').attr("checked", false);
			$(this).parent().removeClass("active");
			$(this).removeClass("active"); }
		else {
			$(this).find('input:checkbox').prop("checked", true);
		}
	});
	*/

	$("#sign_up_one_gender").change(function() {
		if($(this).val() == 1){
			$('#sign_up_one_phone').val(null).parents('.mfrm_field.cf').hide();
		}else{
			$('#sign_up_one_phone').parents('.mfrm_field.cf').show();
		}
		$(".gpic").css("background", $(this).siblings('.graphic_data_' + $(this).val()).val());
	}).change();

	/*$('.hfile input').on('change', function(e) {
		var $label  = $('.hfile input').val();
		$( '.hfile label span' ).html($label);
	});*/




//********* 
	/*$( "#dslider" ).slider({
		value:12,
		min: 0,
		max: 1000,
		step: 1,
		slide: function( event, ui ) {
			$( "#amount" ).val( ui.value );
			var aa = ui.value;
			$("#amt, #amounttool").text(aa);
		}
	});
	$("#amount" ).val( $( "#dslider" ).slider( "value" ));
	var mm = $("#dslider" ).slider("value");
	$("#amt, #amounttool").text(mm);*/








	$('#sign_up_one_region').change(function () {
		modifyUserDataForm($(this), $('#sign_up_one_area'));
	});

	$('#sign_up_one_area').change(function () {
		modifyUserDataForm($(this), $('#sign_up_one_zipCode'));
	});

	$('#profile_one_region').change(function () {
		modifyUserDataForm($(this), $('#profile_one_area'));
	});

	$('#profile_one_area').change(function () {
		modifyUserDataForm($(this), $('#profile_one_zipCode'));
	});

	/*
	$('#quick_search_sidebar_region').change(function () {
	modifySearchForm($(this), $('#quick_search_sidebar_area'));
	});

	$('#quick_search_region').change(function () {
	modifySearchForm($(this), $('#quick_search_area'));
	});
	*/

	$('#advanced_search_region').change(function () {
		modifySearchForm($(this), $('#advanced_search_area'));
	});

	$('.nextstage').click(function(e){
		e.preventDefault();
		if(signUpFormIsValid()){
			$('#next_stage').click();
		}
	});

	$('.contact_submit').click(function(e){
		e.preventDefault();
		if(contactFormIsValid()){
			$('#send').click();
		}
	});

	if($('#save_photo_url').size()){
		var formId = '#profile_form';
		if($(formId).size() == 0){
			formId = '#sign_up_form';
		}
		$(formId).removeAttr('action');
		//$(formId).attr('action', $('#save_photo_url').val());

		/*if(!$('.photos .photo').size()){
			$('#mainPhotoAlreadyExists').val(0);
		}*/

		$('#photo').change(function(){
            /*window.loadImage($(this).val(), function (img) {
                if (img.type === "error") {
                    console.log("couldn't load image:", img);
                } else {
                    window.EXIF.getData(img, function () {
                        var orientation = EXIF.getTag(this, "Orientation");
                        var canvas = window.loadImage.scale(img, {orientation: orientation || 0, canvas: true});
                        $(this).val(canvas.toDataURL());
                        alert(canvas.toDataURL());

                    });
                }
            });*/
           /* var img = this.files[0];
            window.EXIF.getData(img, function () {
                var orientation = EXIF.getTag(this, "Orientation");
                var canvas = window.loadImage.scale(img, {orientation: orientation || 0, canvas: true});
                $(this).val(canvas.toDataURL());

            });
            */
			$(this).closest('form').find('input[type="submit"]').first().click();

		});
		//alert(formId);
		$(formId).ajaxForm({
			url: $('#save_photo_url').val(),
			beforeSend: function() {

				$('.ui.progress').show();

                window.loadImage($('#photo').val(), function (img) {
                    if (img.type === "error") {
                        console.log("couldn't load image:", img);
                    } else {
                        window.EXIF.getData(img, function () {
                            var orientation = EXIF.getTag(this, "Orientation");
                            var canvas = window.loadImage.scale(img, {orientation: orientation || 0, canvas: true});
                            $('#photo').val(canvas.toDataURL());

                        });
                    }
                });

			},
			uploadProgress: function(event, position, total, percentComplete) {
				//var value = Math.round((data.loaded * 100.0) / data.total);
				$('.ui.progress').progress({
					percent: percentComplete,
				});

				$('#upload_photo_label span').text(percentComplete);

				if(percentComplete == 100){
					$('.ui.progress').hide();
					$('#global_dimmer').removeClass("disabled").find('.loader').removeClass("disabled");
				}


			},
			success: function() {
				window.location.href = $('#photos_url').val();
			},
			complete: function(xhr) {
				//status.html(xhr.responseText);
			}

		});

		$('.removePhoto').click(function(e){
			e.preventDefault();
			var id = $(this).attr("id");
			var node = $(this).parents('.photo');

			$('.ui.basic.modal')
				.modal({
					closable: true,
					onApprove : function() {
						deletePhoto(id, node);
					}
				})
				.modal('show')
			;
		});


		$('.mainPhoto.ui.checkbox.toggle').checkbox({
			onChecked: function(){
				setMainPhoto($(this));
			}
		});
	}



	$('#send_sms, #activate, #send_password').click(function (e) {
		e.preventDefault();
		var form = $(this).closest('form');
		if(!form.find('input[type="text"]').val().trim().length){
			return;
		}
		form.submit();
	});


	$('#search_filter').change(function(){
		$('#advanced_search_filter').val($(this).val());
		$('#search_filter_form')
		// change page number to 1 in url when switching filter
			.attr('action', $('#search_url_when_switching_filter').val())
			.find('input[type="submit"]')
			.click()
		;
	});

	$('.usersResults .first a, .usersResults .previous a, .usersResults .page a, .usersResults .next a, .usersResults .last a').click(function(e){
		e.preventDefault();
		var url = $(this).attr('href');

		$('#search_filter_form')
			.attr('action', url)
			.find('input[type="submit"]')
			.click()
		;
	});

	$('.report_abuse').click(function(e){
		//$('body').animate({scrollTop: $('.profile-nav.nav2').offset().top }, 800);

		e.preventDefault();
		$('.report-success').hide();
		var parent_el = $(this).parents('.profile-nav');
		if(!parent_el.hasClass('nav2')){
			parent_el = $(this).parents('.profile-top');
		}
		if(parent_el.size() == 0){
			parent_el = $(this).parents('.buttons_mob');
		}
		$('.report-cont').removeAttr('style');
		$('#nitifShow').remove();


		//if($(this).hasClass('bottom')){
			//$('#user_data').scrollTo(0, $('.popuplogo').offset().top + 100);
			//$('#user_data').animate({scrollTop: parent_el.next('.report-cont').offset().top + 100 }, 800);
		//}
		if($('.profile_mobile').height() > 0){
            parent_el.next('.report-cont').slideDown();
            //$('html, body').height($('.main_container').outerHeight());
            setTimeout(function () {
            	$('html, body').animate({ height: $('.main_container').outerHeight(),scrollTop: $('.profile_mobile').outerHeight() }, 800);
            },400);
            //$('.main_container').scrollTop($('.main_container').outerHeight());
            /*setTimeout(function () {
                //$('html, body').height($('.main_container').outerHeight());
                $('.main_container').scrollTop($('.main_container').outerHeight());
            },400);*/
			//alert($('.profile_mobile').outerHeight());
			/*setTimeout(function () {
                //alert($('.profile_mobile').outerHeight());
                $('html,body').height($('.profile_mobile').outerHeight());
                $('html, body').animate({ scrollTop: $('.profile_mobile').outerHeight() }, 800);
                //alert($('.profile_mobile').outerHeight());
            },400);
*/

		}else{
            parent_el.next('.report-cont').slideDown('slow');
		}

	});

	$('#report, #report1, .report-cont .report').click(function(){
		var memberId = $(this).parents('.report-cont').find('.memberId').val();
		var text = $(this).parents('.report-cont').find('textarea').val();
		var el_sel_suc = '.profile-nav.nav2';
		if($(this).attr('id') == 'report1'){
			el_sel_suc = '.profile-top';
		}
		if($(this).hasClass('report')){
			el_sel_suc = $(this).parents('.report-cont').prev('.buttons_mob');
		}
		//'.profile-top' '.profile-nav.nav2'
		reportAbuse(memberId, text, el_sel_suc);
	});

	$('#report-cancel, #report-cancel1, .report-cont .report-cancel').click(function () {
		$('.report-cont').slideUp('slow');
        setTimeout(function () {
            $('html, body').height($('.main_container').outerHeight());
            //$('html, body').removeAttr('style');
            //$('html, body').animate({ height: $('.main_container').outerHeight(),scrollTop: $('.profile_mobile').outerHeight() }, 800);
        },600);
	});

	$('#more').click(function(e){


	});


    $('.nmobile_footer .more-btn').on('click', function(e){

        console.log(2);

        if($(this).hasClass('menu-left')){
            $(this).removeClass('menu-left');

            $(this).parents('.menu-one').animate({
                'margin-left': '-92%'
            }, 500);
        }else{
            $(this).addClass('menu-left');

            $(this).parents('.menu-one').animate({
                'margin-left': '0'
            }, 500);
        }
    });


	/*$('ul.mobile .submenu, ul.mobile').each(function () {
		$(this).css('transform', 'translate(-' + $(this).outerWidth() + 'px,0)');
	});*/

    $('ul.mobile .settings-icon, ul.mobile .contacts > a').on('click', function (e) {
        //jQuery.fx.interval = 500;

        $(this).parents('ul.mobile').animate({
            scrollTop: 0
        }, 'slow');

		scrollNav($(this).next('.submenu'), 0, '300');
		/*
        $(this).next('.submenu').animate({
            'left': '0',
            'scrollTop': 0
        }, 300, function () {
            //alert($(this).scrollTop());
        });
        */
        return false;
    });

    $('.submenu .back-btn').on('click', function (e) {
        //jQuery.fx.interval = 500;

		scrollNav($(this).parents('.submenu'), -$(this).parents('.submenu').outerWidth(), '300');
		/*
        $(this).parents('.submenu').animate({
            'left': '-100%'
        }, 300);
        */
        return false;
    });

    var mobile = $('.inner_menu ul.mobile');
    var glob_dimmer = $('#global_dimmer');

	/*mobile.on("swipe",function(){
		$(this).animate({
			'left': '-90%'
		}, 300, function () {
			$('.nmobile_footer .send_message_area').toggleClass('hidden');
			glob_dimmer.toggleClass('disabled');
			$('.submenu .back-btn').click();
			//$(this).hide();
		});
	});*/



    $('.inner_menu .tglmenu').click(function (e) {
		var distance = -mobile.outerWidth();
		if(!mobile.hasClass('open')){
			distance = 0;
		}
		scrollNav(mobile, distance, '300');



		/*
		if (mobile.css('left') != '0px') {
			$('.nmobile_footer .send_message_area').toggleClass('hidden');
            //mobile.attr('style', 'display:block !important;');
            mobile.animate({
                'left': '0'
            }, 300, function () {
                glob_dimmer.toggleClass('disabled');
                //$(this).show();
            });

        } else {

            mobile.animate({
                'left': '-90%'
            }, 300, function () {
				$('.nmobile_footer .send_message_area').toggleClass('hidden');
                glob_dimmer.toggleClass('disabled');
                $('.submenu .back-btn').click();
                //$(this).hide();
            });
        }
        */
        return false;
    });

	var settings = $.extend({
		triggerOnTouchEnd   : true,
		swipeStatus         : swipeStatus,
		allowPageScroll     : 'vertical',
		threshold           : 100,
		excludedElements    : 'label, button, input, select, textarea, .noSwipe',
		speed               : 250

	}, {} );

	mobile.swipe(settings);

	$('.nmobile_footer ul').swipe(settings);
	//mobile.find('.submenu').swipe(settings);

	/**
	 * Catch each phase of the swipe.
	 * move : we drag the navigation
	 * cancel : open navigation
	 * end : close navigation
	 */


    glob_dimmer.on('click',function (e) {
        //jQuery.fx.interval = 10;
		scrollNav(mobile, -mobile.outerWidth(), '300');

        /*mobile.animate({
            'left': '-90%'
        }, 300, function () {
			$('.nmobile_footer .send_message_area').toggleClass('hidden');
            glob_dimmer.toggleClass('disabled');
            $('.submenu .back-btn').click();
            //$(this).hide();
        });
        //e.preventDefault();
        */
        return false;
    });

	//console.log("111");

	function swipeStatus(event, phase, direction, distance) {
		if($(event.target).parents('.nmobile_footer').size() == 1){
			var btn = $('.nmobile_footer .more-btn');
            if (phase == 'move' && (direction == 'right') && !btn.hasClass('menu-left')) {
                btn.click();

            } else if (phase == 'move' && direction == 'left' && btn.hasClass('menu-left')) {
                btn.click();
            } else if (phase == 'cancel' && (direction == 'left')) {
                //scrollNav(menu_swipe, 0, 0);
                console.log('cansel');
            } else if (phase == 'end' && (direction == 'left')) {

                console.log('end1');

            } else if ((phase == 'end' || phase == 'cancel') && (direction == 'right')) {
                console.log('end');
            }


		}else {
            var menu_swipe = $(event.target).parents('ul.submenu');
            if (menu_swipe.size() == 0) {
                menu_swipe = mobile;
            }
			/*var menu_swipe = mobile;

			 if(mobile.find('.open.submenu').hasClass('open')){
			 menu_swipe = mobile.find('.open.submenu');
			 //alert(mobile.find('.open.submenu').hasClass('open'));
			 }
			 */
            if (phase == 'start') {
                if (menu_swipe.hasClass('open')) {
                    transInitial = 0;
                } else {
                    transInitial = menu_swipe.outerWidth();
                }
            }
            var mDistance;

            if (phase == 'move' && (direction == 'left')) {
                if (transInitial < 0) {

                    mDistance = transInitial - distance;
                } else {
                    mDistance = -distance;
                }

                //scrollNav(menu_swipe, mDistance, 0);

            } else if (phase == 'move' && direction == 'right') {
                if (transInitial < 0) {
                    mDistance = transInitial + distance;
                } else {
                    mDistance = distance;
                }
                //scrollNav(menu_swipe, mDistance, 0);
            } else if (phase == 'cancel' && (direction == 'left') && transInitial === 0) {
                scrollNav(menu_swipe, 0, 0);
            } else if (phase == 'end' && (direction == 'left')) {

                scrollNav(menu_swipe, -mobile.outerWidth(), '300');

            } else if ((phase == 'end' || phase == 'cancel') && (direction == 'right')) {
                console.log('end');
            }
        }
	}

	function isSafari() {
		return /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
	}

	function isChrome() {
		return /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
	}

	function scrollNav(menu, distance, duration) {
		menu.css('transition-duration', (duration / 1000).toFixed(1) + 's');

		if(distance >= 0) {
			distance = 0;
			if(!menu.hasClass('mobile')){
				$('body .header-main .inner_menu ul.mobile').css({ 'overflow-y': 'hidden' });
			}
            $('html').css({'-webkit-overflow-scrolling': 'auto' });
		}else{
            $('body .header-main .inner_menu ul.mobile').css({ 'overflow-y': 'auto' });
            if (menu.hasClass('mobile')) {
                $('html').css({'-webkit-overflow-scrolling': 'touch'});
            }
		}

		if(distance <= -menu.outerWidth()) {
			distance = -menu.outerWidth();
		}

		if(isSafari() || isChrome()) {
			menu.css('-webkit-transform', 'translate(' + distance + 'px,0)');
		}
		else{
			menu.css('transform', 'translate(' + distance + 'px,0)');
		}
		if(distance == '0') {
			glob_dimmer.removeClass('disabled');
			menu.addClass('open');
		}else {
			if (menu.hasClass('mobile')) {
				glob_dimmer.addClass('disabled');
			}
			if(distance + menu.outerWidth() == 0) {
				menu.removeClass('open');
			}
		}
		if($('.inner_menu ul.mobile').hasClass('open')){
			$('.nmobile_footer .send_message_area').removeAttr('style');
		}else{
			if($('.nmobile_footer .send_message_area textarea').size()) {
                $('.nmobile_footer .send_message_area').css({'display': 'inline-block'});
            }
		}
	}

	if($(window).width() <= 640){
        $('#search_filter_form input[name="is_mobile"]').val(1);
        //setTimeout(function () {
            windowScrollInit($('.mobile_results'));
        //},10000);

	}
	else {
        $('#search_filter_form input[name="is_mobile"]').val(0);
		windowScrollInit($('.desktop_results'));
	}

	favAndBlackListActionsInit();

});



function windowScrollInit($container){
	//alert($container.attr('class'));
	var scrollContainer = window;
	if($container.hasClass('mobile_results')){
		scrollContainer = 'html, body, .main_container';
	}
	//console.log("222");

	$(scrollContainer).scroll(function() {

		if($container.find('.user-section').size() > 0) {
			console.log($('body').scrollTop(),$(window).scrollTop());

			if ($(window).scrollTop() > $container.find('.user-section:last-child').offset().top - 800) {
				//alert($(window).scrollTop());
				$(window).unbind('scroll');
				loadMoreUsers($container);
			}
		}
	});
}

var loadUsers = true;
var usersSendRq = false;

function loadMoreUsers($container) {

    var $form = $('#search_filter_form');
    var page = $('#page').val();
    var url = $form.attr('action') + '/' + page;
    if ($form.find('#page').size() == 1 || $('#page').attr('name') == 'pageNum') {
        url = $form.attr('action');
    }
    if (loadUsers && !usersSendRq) {
        usersSendRq = true;
		$.ajax({
			url: url,
			type: $form.attr('method'),
			data: $form.serialize(),
			success: function (html) {
				//alert($(html).find('.no-result').size());
				if ($(html).find('.no-result').size() == 1) {
					loadUsers = false;
				}
				if ($(html).find('.userId').size()) {
					$('#page').val(parseInt(page) + 1);
					$container.append(html);
					favAndBlackListActionsInit();
                    if(!$container.hasClass('mobile_results')) {
                        windowScrollInit($container);
                    }
				}
                usersSendRq = false;
			}
		});
	}
}

function modifyUserDataForm($select, $replaced_node){
	var $form = $select.closest('form');
	var data = {};
	data[$select.attr('name')] = $select.val();

	if($select.attr('id') == 'sign_up_one_area'){
		data[$('#sign_up_one_region').attr('name')] = $('#sign_up_one_region').val();
	}

	if($select.attr('id') == 'profile_one_area'){
		data[$('#profile_one_region').attr('name')] = $('#profile_one_region').val();
	}

	$.ajax({
		url : $('#form_helper_url').val(),
		type: $form.attr('method'),
		data : data,
		success: function(html) {
			$replaced_node.replaceWith(
				$(html).find('#' + $replaced_node.attr('id'))
			);

			if($select.attr('id') == 'sign_up_one_region'){
				$('#sign_up_one_area').change(function () {
					modifyUserDataForm($(this), $('#sign_up_one_zipCode'));
				});
			}

			if($select.attr('id') == 'profile_one_region'){
				$('#profile_one_area').change(function () {
					modifyUserDataForm($(this), $('#profile_one_zipCode'));
				});
			}

		}
	});
}


function modifySearchForm($select, $replaced_node){
	var $form = $select.closest('form');
	var data = {};
	data[$select.attr('name')] = $select.val();

	/*
	 if($select.attr('id') == 'quick_search_sidebar_area'){
	 data[$('#quick_search_sidebar_region').attr('name')] = $('#quick_search_sidebar_region').val();
	 }

	 if($select.attr('id') == 'quick_search_area'){
	 data[$('#quick_search_region').attr('name')] = $('#quick_search_region').val();
	 }
	 */

	if($select.attr('id') == 'advanced_search_area'){
		data[$('#advanced_search_region').attr('name')] = $('#advanced_search_region').val();
	}


	$.ajax({
		url : $form.find('.form_helper_url').val(),
		type: $form.attr('method'),
		data : data,
		success: function(html) {
			//console.log(html);
			var $node = $(html).find('#' + $replaced_node.attr('id'));
			$node.find('input[type="checkbox"]').attr('checked','checked');
			$replaced_node.replaceWith($node);
			$('#advanced_search_area, .field_name.hidden').show();
		}
	});
}

function deletePhoto(id, node){
	var thisIsMainPhoto = node.find('.mainPhoto input').is(":checked");
	node.remove();
    node.parents('body').trigger('refresh');
    scrollUpload -= 253;

	$.ajax({
		url: '/user/photo/delete/' + id,
		type: 'Post',
		error: function(response){
			console.log("Error:" + JSON.stringify(response));
		},
		success: function(otherPhotoId){

			/*if(parseInt(otherPhotoId) > 0){
				var radiobox = $('.photos .mainPhoto').find('input[value="' + otherPhotoId + '"]');
				radiobox.click();
			}
			if(!$('.photos .photo').size()){
			 	$('#mainPhotoAlreadyExists').val(0);
			}
			*/

			if(thisIsMainPhoto){
				$('.photo').eq(0).find('.mainPhoto').click();
			}



		}
	});
}

function setMainPhoto(thisObj){
	var id = thisObj.val();
	$.ajax({
		url: '/user/photo/main/' + id,
		type: 'Post',
		error: function(response){
			console.log("Error:" + JSON.stringify(response));
		},
		success: function(response){
			//console.log("Success:" + JSON.stringify(response));
		}
	});
}

function getUserData(id){

	$.ajax({
		url: '/user/users/' + id,
		type: 'Get',
		error: function(response){
			//console.log("Error:" + JSON.stringify(response));
		},
		success: function(response){
			//console.log("Success:" + JSON.stringify(response));
			$('#user_data').html(response);
			$('#profile_dimmer').addClass("disabled").find('.loader').addClass("disabled");
		}
	});
}

function listAction(action, memberId, refreshPage, successMessage){

	$('#global_dimmer').removeClass("disabled").find('.loader').removeClass("disabled");

	$.ajax({
		url: '/user/users/' + action + '/' + memberId,
		type: 'Get',
		error: function(response){
			console.log("Error:" + JSON.stringify(response));
		},
		success: function(response){
			//console.log("Success:" + JSON.stringify(response));
			if(refreshPage && $('#search_filter_form').size() != 0){
				$('#search_filter_form')
					.find('input[type="submit"]')
					.click()
				;
			}
			else{
				$('#global_dimmer').addClass("disabled").find('.loader').addClass("disabled");
				$('#alert .content').html('<p>' + successMessage + '</p>');

				$('#alert.ui.modal').modal({onApprove : function() { }}).modal('show');
				//alert(successMessage);
			}

		}
	});
}

function textAlert(text){
	$('#alert .content').html('<p>' + text + '</p>');
    $('#alert.ui.modal .actions .cancel').remove();
	$('#alert.ui.modal').modal({onApprove : function() { }}).modal('show');
}

function textConfirm(text, okFunction) {
    $('#alert .content').html('<p>' + text + '</p>');
    if($('#alert.ui.modal .actions .cancel').size() == 0) {
        $('#alert .actions').append('<div class="ui cancel button">Cancel</div>');
    }
    //<div class="ui cancel button">Cancel</div>
    $('#alert.ui.modal').modal({onApprove : function() {
        new Function(okFunction)();
    }}).modal('show');
}

function favAndBlackListActionsInit(){
	$('.add_to_fav, .add_to_black_list, .delete_from_fav, .delete_from_black_list').unbind('click').bind('click', function(e){
		e.preventDefault();
		//var action = ($(this).hasClass('add_to_fav')) ? 'favorite' : 'black_list';

		var refreshPage = false;
		var send = true;

		if($(this).hasClass('add_to_fav')){
			if($(this).parent('.nyinner').hasClass('inList')){
				send = false;
			}else {
				var action = 'favorite';
				var successMessage = 'User has been added to Favorites';
				$(this).parent('.nyinner').addClass('inList');
			}
		}
		else if($(this).hasClass('add_to_black_list')){
			var action = 'black_list';
			var successMessage = 'User has been blocked';
			$('.add_to_black_list').addClass('hidden');
			$('.delete_from_black_list').removeClass('hidden');
		}
		else if($(this).hasClass('delete_from_fav')){
			var action = 'favorite/delete';
			var successMessage = '';
			refreshPage = true;
		}
		else if($(this).hasClass('delete_from_black_list')){
			var action = 'black_list/delete';
			var successMessage = 'User has been unblocked';
			refreshPage = true;
			$('.add_to_black_list').removeClass('hidden');
			$('.delete_from_black_list').addClass('hidden');
		}
		if(send && !$(this).hasClass('inList')) {
			if(!$(this).hasClass('add_to_black_list') && !$(this).hasClass('delete_from_black_list')) {
				$(this).addClass('inList');
			}
			listAction(action, $(this).parents('.user-section').find('.userId').val(), refreshPage, successMessage);
		}
	});

	$('.like_click').unbind('click').bind('click', function(e){
		e.preventDefault();
		//alert($(this).hasClass('inList'));
		if($(this).hasClass('inList')){
			return false;
		}

		var id = $(this).attr('userId');

		if(parseInt(id) > 0) {

			sendLike(id);

			//if(!$(this).parent().hasClass('nyul_bot')) {
			//	$(this).remove();
			//}
		}
	});

}

function reportAbuse(memberId, text, suc_el_sel){

	if(text.length == ''){
		return;
	}

	$.ajax({
		url: '/user/report/abuse/' + memberId,
		type: 'Post',
		data: 'text=' + text,
		error: function(response){
			//console.log("Error:" + JSON.stringify(response));
		},
		success: function(response){
			//console.log("Success:" + JSON.stringify(response));
			$('.report-cont').slideUp('slow');

			//'profile-top' or '.profile-nav.nav2'
			notificationShow('Thanks! The message was sent to staff of NYRichdate.', suc_el_sel);
			$('.report-cont').find('textarea').val('');
		}
	});
}


function signUpFormIsValid(){

	var isError = false;

	$('.cf').removeClass('error');

	$('[required="required"]').each(function(){

		if(!$(this).val().length){
			$(this).parents('.cf').addClass('error');
			isError = true;
		}
	});

	if($('#sign_up_one_agree').size() && !$('#sign_up_one_agree').is(":checked")){
		$('#sign_up_one_agree').parents('.cf').addClass('error');
		isError = true;
	}

	if(isError){
		$('.errors.empty_fields').show();

		$('html, body').animate({
			scrollTop: $('.errors.empty_fields').offset().top - 130
		}, 1000);

		return false;
	}

	return true;

}

function contactFormIsValid(){

	var isError = false;

	$('.mfrm_field').removeClass('error');

	$('[required="required"]').each(function(){

		if(!$(this).val().length){
			$(this).parents('.mfrm_field').addClass('error');
			isError = true;
		}
	});

    $('[required="required"]').focus(function(){
        $(this).parents('.mfrm_field').removeClass('error');
	});

	if(isError){
		$('.errors.empty_fields').show();

		$('html, body').animate({
			scrollTop: $('.errors.empty_fields').offset().top - 160
		}, 1000);

		return false;
	}

	return true;

}


 
		
