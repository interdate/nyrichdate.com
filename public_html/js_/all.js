jQuery(document).ready(function($) {

	//== form toggle
	$('.logbtn').click(function(e){
        e.preventDefault();
		$('header .form').fadeToggle('fast');
    });
	
	$("#menu a").removeAttr("title");
	$(".menu-item-has-children").hover(function() {
        $(this).children("ul.sub-menu").slideToggle(100)
    });
	
	$(".tglmenu").click(function(e) {
		e.preventDefault();
        $("#menu").toggle();//slideToggle(200)
    });
	
	if($(window).width() <= 640){
		//$('.headerinn').addClass('scroll');
	}
	$(window).on("resize", function() {
        if($(window).width() > 1000 && $("#menu").is(":hidden")) { $("#menu").removeAttr("style") }
    }).trigger("resize");
	
	$(window).scroll(function() {
        $(".scroltop").toggleClass("showscroll", $(document).scrollTop() >= 200);
		$("header").toggleClass("opac", $(document).scrollTop() >= 100);
		$('.headerinn').toggleClass('scroll', $(document).scrollTop() >= 20);
    }), 
	
	$(".scroltop").click(function(e) {
		e.preventDefault();
		$("html,body").animate({scrollTop: 0}, 300);
	});




	//== TABS
	$('.tabs').hide();
	$('.tabnav a').bind('click', function(e){
		$('.tabnav a.active').removeClass('active');
		$('.tabs:visible').hide();
		$(this.hash).fadeIn(200);
		$(this).addClass('active');
		e.preventDefault();
	//}).filter(':nth(1)').click();
	}).filter(':first').click();
	
	//== faq
	$(".faqtitle").click(function(e) {
		e.preventDefault();
		//$(this).closest('.forumfaq').find('.active').removeClass('active');
		$(this).toggleClass('active');
		//$(".fcont").slideUp();
		$(this).next(".fcont").slideToggle(500);
	});			
	//$(".faqtitle:first").click();
	$(".faqtitle:first").addClass('active');
	
	//== slider
	$('.hmslider').slick({
		arrows: false,
		dots: true,
		autoplay: true,
		rtl: true,
		speed: 1000,
	});
	
	//== Popup
	
	$('#user_data').css({'max-height': ($(window).height() * 0.8)})
	
	$('.f1').bind('click', function(e){

        console.log($(this).offset().top);

		e.preventDefault();
		$('#user_data').html('');
		$('#profile_dimmer').removeClass("disabled").find('.loader').removeClass("disabled");
		$(this.hash).fadeIn(300);
		$('.overlay').fadeIn(300);

        var userId = ( $(this).parents('.boxcont').size() )
            ? $(this).parents('.boxcont').find('.userId').val()
            : $(this).siblings('input[type="hidden"]').val()
        ;


        var marTop = $(this).offset().top - 400;
        if(marTop < 0){
            marTop = 0;
        }

        $('.popupmainopen').css('margin-top', marTop);

		getUserData(userId);
	});

	$('.view_my_profile').click(function(e){
        e.preventDefault();
        $('#user_data').html('');
        $('#profile_dimmer').removeClass("disabled").find('.loader').removeClass("disabled");
        $(this.hash).fadeIn(300);
        $('.overlay').fadeIn(300);
        $('.popupmainopen').css('margin-top', 0);
        getUserData($('#my_user_id').val());
    });



	favAndBlackListActionsInit();
	
	$('.close').bind('click', function(e){
		e.preventDefault();
		$(this).parents('.popupmainopen').fadeOut(300);
		$('.overlay').fadeOut(300);
	});
	$('.overlay').click(function(e){
		$('.popupmainopen, .overlay').fadeOut(300);
	});	
	
	// Checkbox
	$('.css-label').click(function() {
        $(this).closest('.chkbox').toggleClass('act');
    });
	
	
	//**********************************************

	// List
	$(".tgllists").click(function(){
	$(".tgllists").toggleClass('tglactive');
    $("#tgllistsbox").slideToggle(300);
	});
	$(window).on('resize', function(){
	if($(window).width() > 801 && $('#tgllistsbox').is(':hidden')) { 
	   $('#tgllistsbox').removeAttr('style'); 
	  }
	});
	
	// Account Management
	$(".tglacc-mng").click(function(){
	$(".tglacc-mng").toggleClass('tglactive');
    $("#tglacc-mngbox").slideToggle(300);
	});
	$(window).on('resize', function(){
		if($(window).width() > 801 && $('#tglacc-mngbox').is(':hidden')) { 
	   $('#tglacc-mngbox').removeAttr('style'); 
	  }
	  //$('.scroltop').css({'right':  $(window).width()/2 - 635 + 'px' });
	}).trigger('resize');
	// Account Management

	
	
	 //== chkbox
	 /*$('.chkbox').click(function () { 
		  $(this).addClass("active");
		  $(this).parent().addClass("active");
		 if ($(this).find('input:checkbox').is(":checked")) { 
		  $(this).find('input:checkbox').attr("checked", false);
		  $(this).parent().removeClass("active");
		  $(this).removeClass("active"); }
		 else {
		   $(this).find('input:checkbox').prop("checked", true);
		 } 
	 });*/
	 

	$('.faqs dd').hide(); // Hide all DDs inside .faqs
	$('.faqs dt').hover(function(){$(this).addClass('hover')},function(){$(this).removeClass('hover')}).click(function(){ // Add class "hover" on dt when hover
	$(this).next().slideToggle('normal'); // Toggle dd when the respective dt is clicked
	}); 
	
	$(".toggleh3").click(function() {
		$(this).toggleClass('deactive');
		$(".toggletext").slideToggle(500);
	});

	/******************** User Photos And Cloudinary *********************************************/

	/*
	$.cloudinary.config({ cloud_name: 'greendate', api_key: '333193447586872'});
	$('.ui.progress').hide();
	//$('#global_dimmer').hide();

	if($('#top_thumb').size()){

        var topThumb = $('#top_thumb').val();
        var currentUserGender = $('#current_user_gender').val();

		var url = (topThumb.length)
			? $.cloudinary.url(topThumb, { width: 23, height: 23, crop: 'thumb', gravity: 'face', format: 'png', radius: 3 })
			: $('#no_photo_url_' +  currentUserGender).val()
        ;

        $('#top_thumb').parents('a').find('img').attr('src',url);

	}



	$('.cloudinary-fileupload').bind('fileuploadstart', function(e, data) {
		$('.ui.progress').show();
		$('.browsebutt, .browseinput').attr("disabled","disabled");
	});

	$('.cloudinary-fileupload').bind('fileuploadprogress', function(e, data) {
		var value = Math.round((data.loaded * 100.0) / data.total);
		$('.ui.progress').progress({
			percent: value,
		});
		$('#upload_photo_label span').text(value);
	});

	$('.cloudinary-fileupload').bind('cloudinarydone', function(e, data) {
		//console.log(JSON.stringify(data));
		$('.browsebutt, .browseinput').removeAttr("disabled");

		$('#global_dimmer').removeClass("disabled").find('.loader').removeClass("disabled");
		//return;
		$('.ui.progress').hide();
		savePhotoData(data);
		return true;
	});
	*/


	if($('#save_photo_url').size()){
		$('#sign_up_form').attr('action', $('#save_photo_url').val());


		$('#photo').change(function(){
			$(this).closest('form').find('input[type="submit"]').click();
		});

		$('#sign_up_form').ajaxForm({
			beforeSend: function() {
				$('.ui.progress').show();
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


	}

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


	$('.photos .mainPhoto.ui.checkbox.toggle').checkbox({
		onChecked: function(){
			setMainPhoto($(this));
		}
	});

/*
	$('.sidebarPhoto, .resultsPhoto').each(function(){

		if($(this).hasClass('sidebarPhoto')){
			var width = 128;
			var height = 154;
		}
		else{
			var width = 184;
			var height = 218;
		}

		var photoName = $(this).siblings('input[type="hidden"]').val();

		var genderId = $(this).parents('.boxcont').find('.userGenderId').val();
        console.log(genderId);

		var url = (photoName.length)
			? $.cloudinary.url(photoName, { width: width, height: height, crop: 'scale' })
			: $('#no_photo_url_' + genderId).val()
		;

		$(this).attr('src',url)
			//.css({"width":width, "height": height})
		;
	});

*/

	/******************** Articles Cloudinary *********************************************/

	if($('.magcont').size() || $('.hp-articles').size()){
		if($('.previewImageName').size()){
			$('.previewImageName').each(function(){
				var url = $.cloudinary.url($(this).val(), { width: 184, height: 218, crop: 'fill' });
				$(this).siblings('img').attr('src',url);
			});
		}

		if($('.imageName').size()){
			var url = $.cloudinary.url($('.imageName').val(), { crop: 'fill' });
			$('.imageName').siblings('img').attr('src',url);
		}


		if($('.homepageImageName').size()){
			$('.homepageImageName').each(function(){
				var url = $.cloudinary.url($(this).val(), { width: 176, height: 176, crop: 'fill' });
				$(this).siblings('img').attr('src',url);
			});
		}
	}

    /******************** Home Page Cloudinary *********************************************/

	if($('.slides').size()){

        $('.imageName').each(function(){
            var url = $.cloudinary.url($(this).val(), {format: 'jpg'});
            $(this).parents('.slide').css('background', 'url(' + url + ') no-repeat top center');
        });

	}


	$('.faceImageName').each(function(){
		var url = $.cloudinary.url($(this).val(), { width: 200, height: 200, crop: 'thumb', gravity: 'face', radius: 'max', format: 'png' })
		//console.log(url);
		$(this).siblings('.url').val(url);
	});


	/******************** Messages Page Cloudinary *********************************************/

	if($('.hotlist').size()){

		$('.hotlist .userimg input[type="hidden"]').each(function(){
			var url = $.cloudinary.url($(this).val(), { width: 86, height: 86, crop: 'thumb', gravity: 'face', format: 'png' })
			//console.log(url);
			$(this).parent().find('img').attr('src',url);
		});

	}











	/**************************************************************************/

	$('.errors ul').addClass("list");

	$('.qs .free').click(function(e){
		e.preventDefault();
		$(this).siblings('input[type="submit"]').click();
	});

	/*
	$('#advancedSearch_withPhoto').click(function(){
		var filter = $(this).is(':checked') ? 'photo' : $('#search_filter_by_default').val();
		$('#advancedSearch_filter').val(filter);
	});
	*/

	$('#searchFilter').change(function(){
		$('#advancedSearch_filter').val($(this).val());
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

	$('.rptimgbox.boxcont').popup();

	$('.tgl_qs').click(function(){
		$(this).toggleClass('open');
		$('#quick_search_sidebar_form').toggle();
	});

    $('.tgl_mqs').click(function(){
    	$(this).toggleClass('open');
		$('.qs').toggle();
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
/*

	$('.bottom_menu .item').bind('touchstart', function(){
		$(this).addClass('active');
	});

	$('.bottom_menu .item').bind('touchend', function(){
		$(this).removeClass('active');
	})

*/










});

function savePhotoData(data){

	var url = $('#save_photo_url').val();
	var mainPhotoAlreadyExists = $('#mainPhotoAlreadyExists').val();

	$.ajax({
		url: url,
		type: 'Post',
		data: 'name=' + data.result.public_id + '&mainPhotoAlreadyExists=' + mainPhotoAlreadyExists,
		error: function(response){
			console.log("Error:" + JSON.stringify(response));
		},
		success: function(response){
			window.location.href = $('#photos_url').val();
		}
	});
}

function deletePhoto(id, node){
	var thisIsMainPhoto = node.find('.mainPhoto input').is(":checked");
	node.remove();

	if(thisIsMainPhoto){
		$('.photo').eq(0).find('.mainPhoto').click();
	}

	$.ajax({
		url: '/user/photo/delete/' + id,
		type: 'Post',
		error: function(response){
			console.log("Error:" + JSON.stringify(response));
		},
		success: function(otherPhotoId){
			
			if(otherPhotoId > 0){
				var radiobox = $('.mainPhoto').find('input[value="' + otherPhotoId + '"]');
				radiobox.click();
			}
			
			if(!$('.photo').size()){
				$('#mainPhotoAlreadyExists').val(0);
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
			if(refreshPage){
				$('#search_filter_form')
					//.attr('action', url)
					.find('input[type="submit"]')
					.click()
				;
			}
			else{
				$('#global_dimmer').addClass("disabled").find('.loader').addClass("disabled");
				alert(successMessage);
			}

		}
	});
}

function favAndBlackListActionsInit(){
	$('.add_to_fav, .add_to_back_list, .delete_from_fav, .delete_from_black_list').unbind('click').bind('click', function(e){
		e.preventDefault();
		//var action = ($(this).hasClass('add_to_fav')) ? 'favorite' : 'black_list';

		var refreshPage = false;

		if($(this).hasClass('add_to_fav')){
			var action = 'favorite';
			var successMessage = 'משתמש זה הוסף לרשימת המועדפים.';
		}
		else if($(this).hasClass('add_to_back_list')){
			var action = 'black_list';
			var successMessage = 'משתמש זה הוסף לרשימת החסומים.';
		}
		else if($(this).hasClass('delete_from_fav')){
			var action = 'favorite/delete';
			var successMessage = '';
			refreshPage = true;
		}
		else if($(this).hasClass('delete_from_black_list')){
			var action = 'black_list/delete';
			var successMessage = '';
			refreshPage = true;
		}

		listAction(action, $(this).parents('.boxcont').find('.userId').val(), refreshPage, successMessage);
	});
}


function signUpFormIsValid(){

    var isError = false;

    $('.field').removeClass('error');

    $('[required="required"]').each(function(){

        if(!$(this).val().length){
            $(this).parents('.field').addClass('error');
            isError = true;
        }
    });

	if($('#signUpOne_agree').size() && !$('#signUpOne_agree').is(":checked")){
        $('#signUpOne_agree').parents('.field').addClass('error');
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

    $('.field_2').removeClass('error');

    $('[required="required"]').each(function(){

        if(!$(this).val().length){
			$(this).parents('.field_2').addClass('error');
            isError = true;
        }
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
			console.log(html);
			var $node = $(html).find('#' + $replaced_node.attr('id'));
			$node.find('input[type="checkbox"]').attr('checked','checked');
			$replaced_node.replaceWith($node);
		}
	});
}