/*******indexOf support for IE < 9 ******* */

if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}


/**********************************/






dialog = false;



$(document).ready(
	function($){




        $.cloudinary.config({ cloud_name: 'greendate', api_key: '333193447586872'});
        var thumbName = $('.hotitle .thumb input[type="hidden"]').val();
        if(thumbName != ""){
            var thumbUrl = $.cloudinary.url(thumbName, { width: 32, height: 32, crop: 'thumb', gravity: 'face' })
            $('.hotitle .thumb img').attr('src', thumbUrl);
        }

		blinkingTitle = false;



		if($('#dialogs').size() && $(window).width() > 1020){
			$('#dialogs').perfectScrollbar({
				wheelSpeed: 35,
				minScrollbarLength: 30
			});

			$('#dialogs').find('.ps-scrollbar-x-rail').hide();
		}


		if($('#dialog').size()){
			var definition = {smile:{title:"Smile",codes:[":)",":=)",":-)","=)"]},"sad-smile":{title:"Sad Smile",codes:[":(",":=(",":-("]},"big-smile":{title:"Big Smile",codes:[":D",":=D",":-D",":d",":=d",":-d"]},cool:{title:"Cool",codes:["8)","8=)","8-)","B)","B=)","B-)","(cool)"]},wink:{title:"Wink",codes:[":o",":=o",":-o",":O",":=O",":-O"]},crying:{title:"Crying",codes:[";(",";-(",";=("]},sweating:{title:"Sweating",codes:["(sweat)","(:|"]},speechless:{title:"Speechless",codes:[":|",":=|",":-|"]},kiss:{title:"Kiss",codes:[":*",":=*",":-*"]},"tongue-out":{title:"Tongue Out",codes:[":P",":=P",":-P",":p",":=p",":-p"]},blush:{title:"Blush",codes:["(blush)",":$",":-$",":=$",':">']},wondering:{title:"Wondering",codes:[":^)"]},sleepy:{title:"Sleepy",codes:["|-)","I-)","I=)","(snooze)"]},dull:{title:"Dull",codes:["|(","|-(","|=("]},"in-love":{title:"In love",codes:["(inlove)"]},"evil-grin":{title:"Evil grin",codes:["]:)","(grin)"]},talking:{title:"Talking",codes:["(talk)"]},yawn:{title:"Yawn",codes:["(yawn)","|-()"]},puke:{title:"Puke",codes:["(puke)",":&",":-&",":=&"]},"doh!":{title:"Doh!",codes:["(doh)"]},angry:{title:"Angry",codes:[":@",":-@",":=@","x(","x-(","x=(","X(","X-(","X=("]},"it-wasnt-me":{title:"It wasn't me",codes:["(wasntme)"]},party:{title:"Party!!!",codes:["(party)"]},worried:{title:"Worried",codes:[":S",":-S",":=S",":s",":-s",":=s"]},mmm:{title:"Mmm...",codes:["(mm)"]},nerd:{title:"Nerd",codes:["8-|","B-|","8|","B|","8=|","B=|","(nerd)"]},"lips-sealed":{title:"Lips Sealed",codes:[":x",":-x",":X",":-X",":#",":-#",":=x",":=X",":=#"]},hi:{title:"Hi",codes:["(hi)"]},call:{title:"Call",codes:["(call)"]},devil:{title:"Devil",codes:["(devil)"]},angel:{title:"Angel",codes:["(angel)"]},envy:{title:"Envy",codes:["(envy)"]},wait:{title:"Wait",codes:["(wait)"]},bear:{title:"Bear",codes:["(bear)","(hug)"]},"make-up":{title:"Make-up",codes:["(makeup)","(kate)"]},"covered-laugh":{title:"Covered Laugh",codes:["(giggle)","(chuckle)"]},"clapping-hands":{title:"Clapping Hands",codes:["(clap)"]},thinking:{title:"Thinking",codes:["(think)",":?",":-?",":=?"]},bow:{title:"Bow",codes:["(bow)"]},rofl:{title:"Rolling on the floor laughing",codes:["(rofl)"]},whew:{title:"Whew",codes:["(whew)"]},happy:{title:"Happy",codes:["(happy)"]},smirking:{title:"Smirking",codes:["(smirk)"]},nodding:{title:"Nodding",codes:["(nod)"]},shaking:{title:"Shaking",codes:["(shake)"]},punch:{title:"Punch",codes:["(punch)"]},emo:{title:"Emo",codes:["(emo)"]},yes:{title:"Yes",codes:["(y)","(Y)","(ok)"]},no:{title:"No",codes:["(n)","(N)"]},handshake:{title:"Shaking Hands",codes:["(handshake)"]},heart:{title:"Heart",codes:["(h)","<3","(H)","(l)","(L)"]},"broken-heart":{title:"Broken heart",codes:["(u)","(U)"]},mail:{title:"Mail",codes:["(e)","(m)"]},flower:{title:"Flower",codes:["(f)","(F)"]},rain:{title:"Rain",codes:["(rain)","(london)","(st)"]},sun:{title:"Sun",codes:["(sun)"]},time:{title:"Time",codes:["(o)","(O)","(time)"]},music:{title:"Music",codes:["(music)"]},movie:{title:"Movie",codes:["(~)","(film)","(movie)"]},phone:{title:"Phone",codes:["(mp)","(ph)"]},coffee:{title:"Coffee",codes:["(coffee)"]},pizza:{title:"Pizza",codes:["(pizza)","(pi)"]},cash:{title:"Cash",codes:["(cash)","(mo)","($)"]},muscle:{title:"Muscle",codes:["(muscle)","(flex)"]},cake:{title:"Cake",codes:["(^)","(cake)"]},beer:{title:"Beer",codes:["(beer)"]},drink:{title:"Drink",codes:["(d)","(D)"]},dance:{title:"Dance",codes:["(dance)","\\o/","\\:D/","\\:d/"]},ninja:{title:"Ninja",codes:["(ninja)"]},star:{title:"Star",codes:["(*)"]},mooning:{title:"Mooning",codes:["(mooning)"]},finger:{title:"Finger",codes:["(finger)"]},bandit:{title:"Bandit",codes:["(bandit)"]},drunk:{title:"Drunk",codes:["(drunk)"]},smoking:{title:"Smoking",codes:["(smoking)","(smoke)","(ci)"]},toivo:{title:"Toivo",codes:["(toivo)"]},rock:{title:"Rock",codes:["(rock)"]},headbang:{title:"Headbang",codes:["(headbang)","(banghead)"]},bug:{title:"Bug",codes:["(bug)"]},fubar:{title:"Fubar",codes:["(fubar)"]},poolparty:{title:"Poolparty",codes:["(poolparty)"]},swearing:{title:"Swearing",codes:["(swear)"]},tmi:{title:"TMI",codes:["(tmi)"]},heidy:{title:"Heidy",codes:["(heidy)"]},myspace:{title:"MySpace",codes:["(MySpace)"]},malthe:{title:"Malthe",codes:["(malthe)"]},tauri:{title:"Tauri",codes:["(tauri)"]},priidu:{title:"Priidu",codes:["(priidu)"]}};
			$.emoticons.define(definition);
			$('.emoticons_wrapper').html($.emoticons.toString());

			console.log($.emoticons.replace($('#dialog').html()));

			$('#dialog').html($.emoticons.replace($('#dialog').html()));
		}

		
		if($('#dialog').size() && $(window).width() > 1020){



            Messenger.qtipInit($('#show_emoticons'), $('#emoticonsTemplate'), Messenger.emoticonsBindClickEvent, 'Insert emoticon');
			
			$('#dialog').perfectScrollbar({
				wheelSpeed: 35,
				minScrollbarLength: 30
			});

			$("#dialog").scrollTop( $( "#dialog" ).prop( "scrollHeight" ) );
			$("#dialog").perfectScrollbar('update');

            $('.dialogInput textarea').keypress(function(e){
                if(dialog.enterKeyPressed(e)){
                    dialog.sendMessage($(this));
                }
            });

		}

		if($('#dialog').size() && $(window).width() < 1020){
			$('footer, .greybox2.ad').hide();
			$('textarea').autogrow({vertical: true, horizontal: false});
			$('html, body').scrollTop($(document).height());
		}
		
		
	}
);


window.onload = function(){
	console.log("Loaded");
	Messenger.init();

	dialog = false;

	var contactId = $('#contactId').val();

	if(contactId){

		var isMobile = $(window).width() < 1020;

		var options = {
			contactId: contactId,
			contactName: $('#contactNickname').val(),
			userName: $('#userNickname').val(),
			creatingInCycle: false,
			isMobile: isMobile
		};

		Dialog.prototype = new Chat(options);
		Dialog.prototype.constructor = Dialog;
		dialog = new Dialog(options);
		$('#dialog .unreadMessage').each(function(){
			var message = {
				id: $(this).val(),
				from: Messenger.currentUserId,
				text: "",
				userImage: Messenger.currentUserImage,
				dateTime: "",
				isSaved: true,
			};
			Messenger.addUnreadMessageId(message);
		});

		Messenger.checkActiveWindowsNewMessages();


	}

};




function Dialog(options){
	
	this.sentMessagesArea = $('#dialog');
	this.username = options.userName; 
	this.newMessagesRequest = '';
	this.isMobile = options.isMobile;
	
	//Chat.apply(this, options);	
	this.insertMessage = function(message){		
		this.processMessage(message);
		var html = Messenger.dialogMessageTemplate.replace(/\[MESSAGE\]/g, message.text);
		var direction = (message.from == this.contactId) ? "contact" : "user";
		var status = this.getMessageStatus(message);
		html = html.replace(/\[STATUS\]/, status);
		html = html.replace(/\[DIRECTION\]/, direction);		
		html = html.replace(/\[DATE_TIME\]/, message.dateTime);
		html = html.replace(/\[MESSAGE_SECTION_ID\]/, message.id);
				
		html = $.emoticons.replace(html);
		
		
		if(this.isMobile){
			this.sentMessagesArea.append(html);
			$('html, body').scrollTop($(document).height());
			$('.message_area textarea').css('height','50px');
		}
		else{
			this.sentMessagesArea
				.append(html)
				.scrollTop( $( "#dialog" ).prop( "scrollHeight" ))
				.perfectScrollbar('update')
			;
		}
	};
	
	this.needToCloneMessage = function(message){
		var chat = Messenger.getChat(this.contactId);		
		if(chat){									
			return true;		
		}
		
		return false;
	};
	
	this.cloneMessage = function(message){
		var chat = Messenger.getChat(this.contactId);
		chat.insertMessage(message);
	};
	
	this.updateMessageSection = function(sectionId, message){

        $('#dialogMessageSection_' + sectionId)
            .addClass('dialogMessageSection_' + message.id)
            .addClass(Messenger.messageStatus.saved)
            .find('.dateTime')
            .text(message.dateTime)
        ;

		this.sentMessagesArea
			.scrollTop( $( "#dialog" ).prop( "scrollHeight" ))
			.perfectScrollbar('update')
		;

		if(this.needToCloneMessage(message)){
			$('#chatMessageSection_'+sectionId).addClass('chatMessageSection_' + message.id);
			$('#chatMessageSection_'+sectionId).find('.dateTime').text(message.dateTime);
			$('#chatMessageSection_'+sectionId).find('.status').html(Messenger.messageStatus.saved);
			$('#chatMessageSection_'+sectionId).find('.userPicture').attr('src', message.senderImage);
		}

	};
	
	this.enterKeyPressed = function(e){				
		return false;
	};
	
}





function Chat(options){	
	this.contactId = options.contactId;
	this.contactName = options.contactName;
	this.creatingInCycle = options.creatingInCycle;
	this.chatWraper;
	this.scrollBarWraper;
	this.sentMessagesArea;	
	this.isMinimized;
	this.waitingMessages = [];
	//this.unreadMessagesFromUser = [];
	this.allowedToReadMessage;
	this.isMobile = false;
	
	this.getContactId = function(){
		return this.contactId;		
	};
	
	this.getContactName = function(){
		return this.contactName;		
	};
	
	this.open = function(){
		
		
		var chat = Messenger.getChat(this.contactId);
		if(!chat){
			
			Messenger.setChat(this);	
			console.log("OPEN");
			
			$.ajax({
				url: '/messenger/chat/open/userId:' + Messenger.currentUserId + '/contactId:' + this.contactId,
				headers: { 'apiKey': Messenger.apiKey },
				//url: '/chat/index.php?openChat=true&userId='+Messenger.currentUserId+'&contactId='+this.contactId,
				timeout:80000,
				dataType: 'json',
				context: this,
				error:function(error){
					console.log(JSON.stringify(error));
					//$('.error').html(error.responseText);
				},
				success: function(response, status){
						
						var html = Messenger.template.html.replace(/\[CONTACT_ID\]/g, this.contactId);					
						html = html.replace(/\[CONTACT_NAME\]/g, this.contactName);
						
						var activeChatsNumber = Messenger.getAllChats().length;
						//var position = Messenger.calculateChatPosition(activeChatsNumber - 1);
						
						var thisChat = this;
						
						this.chatWraper = $('.chatsArea').append(html).find('.chatWindow:last-child');
						
						this.setOverAll();
						
						this.chatWraper
							.click(function(){
								thisChat.setOverAll();
							})
							//.css({"right":position.x, "bottom":position.y, "z-index":100})						
							.find('.close')						
							.click(function(){
								thisChat.close();
							})						
							.siblings('.minimize, .header')						
							.click(function(){
								thisChat.minimize();
							});
						
						this.chatWraper.find('textarea').keypress(function(e){
							if(thisChat.enterKeyPressed(e)){
								thisChat.sendMessage($(this));
							}
						});										
						
						this.scrollBarWraper = this.chatWraper.find('.scrollbar1');
						this.scrollBarWraper.tinyscrollbar();	
						this.sentMessagesArea = this.scrollBarWraper.find('.overview');
						
						this.sentMessagesArea.initHeight = 245;
						
						this.sentMessagesArea.needsToBeScrolled = function(){	
							return (this.height() > this.initHeight) ? true : false;
						};					
						
						this.sentMessagesArea.scroll = function(){
							var height = this.height();
							var initHeight = this.initHeight;						
							thisChat.scrollBarWraper.tinyscrollbar_update(height - initHeight + 10);
						};					
						
						//console.log(JSON.stringify(response.chatHistory));
						
						if(this.creatingInCycle)				
							this.minimize();						
						
						var isNew = false;
						
						Messenger.currentUserHasPoints = response.currentUserHasPoints;
						
						if(response.chatHistory.length > 0){
							for(var i in response.chatHistory){
								var message = response.chatHistory[i];
								this.insertMessage(message);							
								//alert(message.isRead);							
								if(message.from == this.contactId && !message.isRead){
									isNew = true;
									if(!this.isMinimized && this.allowedToReadMessage)							
										this.setMessageAsRead(message);
									else
										this.addMessageToWaiting(message);								
								}
							}				
						}
						
						if(isNew && this.isMinimized){
							this.blinkingStart();
							Messenger.blinkingTitleStart();							
							$('body').append('<embed src="/assets/frontend/media/newMessage.mp3" autoplay="true" autostart="true" type="audio/x-wav" width="1" height="1">');
						}	
						
						if(this.sentMessagesArea.needsToBeScrolled()){						
							this.sentMessagesArea.scroll();
						}
						
						if(!this.creatingInCycle)				
							Messenger.checkActiveWindowsNewMessages();
						else
							setTimeout(function(){
								Messenger.checkActiveWindowsNewMessages();  
							}, 200);
							
						
					//}
						
						Messenger.relocateChats();
					
				}
				
			});
		
		}
		else{
			if(!this.creatingInCycle)				
				Messenger.checkActiveWindowsNewMessages();
			else
				setTimeout(function(){
					Messenger.checkActiveWindowsNewMessages();  
				}, 200);
		}
	};	
	
	
	this.close = function(){
		
		/*
		if(Messenger.newMessagesRequest != ''){
			Messenger.newMessagesRequest.abort(); 					
		}
		*/

		
		console.log('/messenger/chat/close/userId:' + Messenger.currentUserId + '/contactId:' + this.contactId);
		//return;
		
		$.ajax({
			url: '/messenger/chat/close/userId:' + Messenger.currentUserId + '/contactId:' + this.contactId,
			headers: { 'apiKey': Messenger.apiKey },
			//url: '/chat/index.php?closeChat=true&userId='+Messenger.currentUserId+'&contactId='+this.contactId,
			error:function(error){
				console.log("CLOSE CHAT ERROR:" + JSON.stringify(error));
				//$('.error').html(error.responseText);
			},
			timeout:80000,
			dataType: 'json',
			context: this,
			success: function(response, status){	
				console.log("CLOSE CHAT SUCCESS: " + JSON.stringify(response));
				if(response.success){					
					$('.chatsArea').find('#'+this.contactId).remove();					
					Messenger.unsetChat(this);
					Messenger.relocateChats();
					//Messenger.checkActiveWindowsNewMessages();
					Messenger.blinkingTitleStop();
				}
			}
		});
		
	};
	
	this.minimize = function(){
		$('#'+this.contactId).find('.body, textarea').toggleClass('hiden');
		this.isMinimized = $('#'+this.contactId).find('.body, textarea').hasClass('hiden');
		
		if(!this.isMinimized){
			
			if(this.allowedToReadMessage && this.waitingMessages.length > 0){
				for(var i in this.waitingMessages){
					var message = this.waitingMessages[i];
					this.setMessageAsRead(message);
				}
			}
			
			this.waitingMessages = [];
			
			if(this.sentMessagesArea.needsToBeScrolled()){						
				this.sentMessagesArea.scroll();
			}
			
			this.blinkingStop();
			Messenger.blinkingTitleStop();
			
			this.setOverAll();
			
		}
		
	};
	
	this.setOverAll = function(){
		$('.chatWindow').css({"z-index":10});
		$('#'+this.contactId).css({"z-index":100});
	};
	
	this.enterKeyPressed = function(e){
		if (e.keyCode == 13) {	       
			return true;
	    }		
		return false;
	};	
	
	this.insertMessage = function(message){
		
		this.processMessage(message);
		
		var html = Messenger.chatMessageTemplate.replace(/\[MESSAGE\]/g, message.text);		
		html = html.replace(/USER_PICTURE/, message.userImage);
				
		html = html.replace(/\[DATE_TIME\]/, message.dateTime);
		
		var status = this.getMessageStatus(message);
		html = html.replace(/\[STATUS\]/, status);
		
		var direction = (message.from == this.contactId) ? "in" : "out";
		html = html.replace(/\[DIRECTION\]/, direction);
		
		var profileId = (message.from == this.contactId) ? this.contactId : Messenger.currentUserId;		
		html = html.replace(/\[PROFILE_ID\]/, profileId);
		
		html = html.replace(/\[MESSAGE_SECTION_ID\]/, message.id);
		
		html = $.emoticons.replace(html);
		
		this.sentMessagesArea.append(html);	
		if(this.sentMessagesArea.needsToBeScrolled()){						
			this.sentMessagesArea.scroll();
		}
		
		/*
		if( && dialog && this.contactId == dialog.contactId){									
			//dialog.insertMessage(message);		
		}
		*/
		
	};
	
	this.processMessage = function(message){ 
		if(message.from == this.contactId){
			//console.log(message.allowedToRead);
			//console.log(message.text.length);
			this.allowedToReadMessage = message.allowedToRead;
		}
		else{
			Messenger.addUnreadMessageId(message);
		}
		
		message.text = (!message.text.length && !this.allowedToReadMessage)
			? (Messenger.currentUserHasPoints) 
				? $('#paymentText').val() + ' <a href="' + $('#paymentLink').val() + '">' + $('#paymentLinkText').val() + '</a> or <a onclick="Messenger.useFreePointToReadMessage(this)" class="usePoint">' + $('#pointsLinkText').val() + '</a>'
				: $('#paymentText').val() + ' <a href="' + $('#paymentLink').val() + '">' + $('#paymentLinkText').val() + '</a>'
			: message.text.replace(/(?:(https?\:\/\/[^\s]+))/m,'<a href="$1" target="_blank">$1</a>')
		;
	};
	/*
	this.unreadMessagesFromUser.add = function(message){
		if(!message.isRead && message.isSaved && this.indexOf(message) == -1){
			this.push(message);
			console.log("UNREAD:" + JSON.stringify(message));
		}				
	};
	*/
	
	this.getMessageStatus = function(message){		
		return	(message.from == Messenger.currentUserId)
			? (message.isSaved) 
				? (message.isRead) 
					? Messenger.messageStatus.read 
					: Messenger.messageStatus.saved
				: Messenger.messageStatus.sent	
			: ''
		;
	};
	
	this.addMessageToWaiting = function(message){
		this.waitingMessages.push(message);
	};
	
	this.blinkingStart = function(){
		$('#'+this.contactId).find('.header').addClass('blinking');
	};
	
	this.blinkingStop = function(){
		$('#'+this.contactId).find('.header').removeClass('blinking');
	};
	
	this.setMessageAsRead = function(message){
		
		console.log("SET AS READ REQUEST");
		
		$.ajax({
			url: '/messenger/message/read/messageId:' + message.id + '/userId:' + Messenger.currentUserId + '/contactId:' + this.contactId,
			headers: { 'apiKey': Messenger.apiKey },
			//url: '/chat/index.php?setMessageAsRead=true&userId='+Messenger.currentUserId+'&contactId='+this.contactId+'&messageId='+message.id,
			timeout:10000,
			dataType: 'json',
			context: this,
			success: function(response, status){
				console.log("SET AS READ RESPONSE");
				if(response.success){
					console.log("SET AS READ");					
				}
			}
		});
		
	};

	this.needToCloneMessage = function(message){
		return (dialog && this.contactId == dialog.contactId) ? true : false;
	};
	
	this.cloneMessage = function(message){		
		dialog.insertMessage(message);
	};
	
	this.sendMessage = function(textarea){		
		
		var message = textarea.val().replace(/(?:\r\n|\r|\n)/g, '<br />');
		
		if(message.length == 0){			
			return false;
		}
		
		textarea.val('');
		var messageOptions = {
			id: Messenger.createRandomId(),
			from: Messenger.currentUserId,
			text: message,
			userImage: Messenger.currentUserImage,			
			dateTime: "",
			isSaved: false,
		};
		
		this.insertMessage(messageOptions);
		
		if(this.needToCloneMessage(messageOptions)){
			this.cloneMessage(messageOptions);
		}
		
		/*
		if(this instanceof Dialog === false && dialog && this.contactId == dialog.contactId){			
			dialog.insertMessage(messageOptions);
		}
		else{
			this.insertMessage(messageOptions);
		}
		*/
		
		console.log('START SENDING');
				
		$.ajax({
			url: '/messenger/message/send/userId:' + Messenger.currentUserId + '/contactId:' + this.contactId,
			headers: { 'apiKey': Messenger.apiKey },
			//url: '/chat/index.php?sendMessage=true&userId='+Messenger.currentUserId+'&contactId='+this.contactId,
			timeout:80000,
			dataType: 'json',
			type: 'Post',
			data: 'message='+encodeURIComponent(message),
			context: this,
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
			error: function(response){				
				console.log(JSON.stringify(response));
				//$('.error').html(response.responseText);
				//alert('הודעה לא נשלחה. תנסו שוב.');
			},
			success: function(response, status){
				//console.log(JSON.stringify(response));
                //return;

				if(response.success){
					console.log('MESSAGE:' + JSON.stringify(response.message));
					console.log('RESPONSE:' + JSON.stringify(response));
					console.log('END SENDING');					
					var message = response.message;										
					Messenger.addUnreadMessageId(message);					
					this.updateMessageSection(messageOptions.id, message);					
					Messenger.currentUserImage = message.senderImage;
				}
				else{
					
					if(response.contactIsFrozen){
						alert('This message was not sent because it uses froze his account.');
						return;
					}
					
					if(response.chatIsForbidden){
						alert("This message was not sent because this user in your blocked list or you is in its blocked list. If his / her wish to remove this person from your block has access account management and select Blocked list management.");
						return;
					}

					if(response.chatIsNotActive > 0){
						if(response.chatIsNotActive == 1){
							alert("This message was not sent because this user has been blocked by the administrator.");
						}else{
							alert("This message was not sent because your account has been blocked by the administrator.");
							window.location.href = '/';
						}
						return;
					}

                    if(response.isLimit){
                        alert("You have reached the maximum amount of messaging today.");
                        return;
                    }
					
					
					//console.log(JSON.stringify(response));
					//alert('הודעה לא נשלחה. תנסו שוב.');
					//alert(JSON.stringify(response));
					alert('ERROR. Unknown reason.');
				}				
								
			}
		});
		
	};
	
	
	this.updateMessageSection = function(sectionId, message){
		$('#chatMessageSection_'+sectionId).addClass('chatMessageSection_' + message.id);
		$('#chatMessageSection_'+sectionId).find('.dateTime').text(message.dateTime);
		$('#chatMessageSection_'+sectionId).find('.status').html(Messenger.messageStatus.saved);
		$('#chatMessageSection_'+sectionId).find('.userPicture').attr('src', message.senderImage);
		
		if(this.needToCloneMessage(message)){
			/*
            $('#dialogMessageSection_'+sectionId).addClass('dialogMessageSection_' + message.id);
			$('#dialogMessageSection_'+sectionId).find('.dateTime').text(message.dateTime);
			$('#dialogMessageSection_'+sectionId).find('.status').html(Messenger.messageStatus.saved);
			$('#dialogMessageSection_'+sectionId).find('.userPicture').attr('src', message.senderImage);
			*/

            $('#dialogMessageSection_' + sectionId)
                .addClass('dialogMessageSection_' + message.id, Messenger.messageStatus.saved)
                .find('.dateTime')
                .text(message.dateTime)
            ;
		}
	};
	
}










var Messenger = {		
	
	currentUserId : '',
	currentUserImage: '',
	currentUserHasPoints: false,
	chats : [],
	//activeSessions : [],
	templateHolder: '',
	template : {},
	chatMessageTemplate : '',
	dialogMessageTemplate : '',
	newMessagesRequest : '',
	docTitle : document.title,
	soundPlayed: false,
	messageStatus: {},
	unreadMessagesIds: [],
	readMessagesRequest : '',
	apiKey: '',
	
		
	init: function(){
		
		$.ajaxSetup({ 
			/*
			headers: { 'apiKey': $('#apiKey').val() },	
			beforeSend: function(jqXHR, settings){
				//console.log("SETTINGS: " + JSON.stringify(settings));
				//jqXHR.setRequestHeader("apiKey", $('#apiKey').val());
			},
			complete: function(){
				//delete $.ajaxSettings.headers["apiKey"];
			},
			*/
			cache: false
		});
		
		Messenger.apiKey = $('#apiKey').val(),		
		Messenger.currentUserId = $('#currentUserId').val(),
		Messenger.currentUserImage = $('#currentUserImage').val(),
		Messenger.templateHolder = $('#chatTemplate'),
		Messenger.template = {
			html: Messenger.templateHolder.html(),
			width: Messenger.templateHolder.find('.chatWindow').width(),
			header: {
				height: Messenger.templateHolder.find('.header').height() 
			}
		};
		
		Messenger.chatMessageTemplate = $('#chatMessageSectionTemplate').html();
		Messenger.dialogMessageTemplate = $('#dialogMessageSectionTemplate').html();
		
		Messenger.messageStatus = {
			/*
			sent: $('#messageStatusSent').html(),
			saved: $('#messageStatusSaved').html(),
			read: $('#messageStatusRead').html(),
            */
            sent: "sent",
			saved: "unread",
			read: "read",
		};

		if($(window).width() < 1020){
			Messenger.checkNewMessagesMobile();
		}
		else{
			Messenger.checkNewMessages();
		}



				

		//Messenger.openChatsByActiveSessions();
		Messenger.checkMessagesIfRead();
		/*		
		$(window).resize(function(){
			Messenger.relocateChats();
		});
		*/
		
	},
	
	getAllChats: function(){
		return this.chats;
	},
	
	getChat: function(contactId){	
		if(Messenger.chats.length > 0){		
			for(var i in Messenger.chats){
				var chat = Messenger.chats[i];
				if(chat.contactId == contactId){
					return chat;
				}
			}
		}
		
		return false;
	},
	
	setChat: function(chat){		
		Messenger.chats.push(chat);
	},
	
	unsetChat: function(chat){
		
		var index = Messenger.chats.indexOf(chat);
		if (index > -1) {
			Messenger.chats.splice(index, 1);
		}
	},
	
	relocateChats: function(){		
		for(var i in Messenger.chats){
			var chat = Messenger.chats[i];
			var position = Messenger.calculateChatPosition(i);
			$('.chatsArea').find('#'+chat.contactId).css({"right":position.x, "bottom": position.y});
		}	
	},
	
	calculateChatPosition: function(i){
		
		var chatsNumberInRow = Math.floor($(window).width() / (Messenger.template.width + 20) );
		var currentRowIndex =  Math.floor( i / chatsNumberInRow );		
		
		var chatsNumberIncurrentRow = i - (chatsNumberInRow * currentRowIndex);
		var chatPositionX = chatsNumberIncurrentRow * Messenger.template.width + (chatsNumberIncurrentRow * 20) + 20;
		
		if(chatPositionX == 0){
			chatPositionX = 20;
		}
		
		var chatPositionY = currentRowIndex * Messenger.template.header.height + 20;
		
		if(chatPositionY > 20){
			chatPositionY += currentRowIndex * 20;       
		}
		
		return position = {
			x:chatPositionX,
			y:chatPositionY 
		};
	},
	  
		
	checkNewMessages: function(){
		
		console.log("START CHECK NEW MESSAGES");
		
		$.ajax({
			url: '/messenger/newMessages/userId:' + Messenger.currentUserId,
			headers: { 'apiKey': Messenger.apiKey },
			//url: '/chat/index.php?checkNewMessages=true&userId='+Messenger.currentUserId,
			timeout:80000,
			dataType: 'json',
			//data: 'message='+message,
			context: this,
			error: function(response){
				console.log('ABORT CHECK NEW MESSAGES');
				console.log(JSON.stringify(response));
				//Messenger.checkNewMessages();
				//$('.error').html(response.responseText);
			},
			success: function(newMessages, status){				
				console.log("newMessages function response: " + JSON.stringify(newMessages));
				console.log('END CHECK NEW MESSAGES');
				
				if(newMessages.fromUsers.length > 0){
					for(var i in newMessages.fromUsers){
						var user = newMessages.fromUsers[i];
						var options = {
							contactId: user.id,
							contactName: user.name,
							creatingInCycle: true
						};
						
						if(!dialog || (dialog && user.id != dialog.contactId) ){
							//var chat = new Chat(options);
							//console.log("NEW MESSAGES");
							//chat.open();
                            Lobibox.notify('success', {
                                title: user.name,
                                img: user.photo,
                                msg: user.message + '<input type="hidden" value="/user/messenger/dialog/open/userId:' + Messenger.currentUserId + '/contactId:' + user.id + '">',
                                delay: '6000',
                                closable: false,
                                position: 'left bottom',
                                showClass: 'fadeInUp',
                                hideClass: 'fadeOutDown',

                            });

                            //Messenger.setMessageAsNotified(user.messageId);

						}						
					}
					
					//Messenger.checkActiveWindowsNewMessages();
				}

                $('.lobibox-notify').click(function(){
                    var url = $(this).find('input[type="hidden"]').val();
                    window.location.href = url;
                });
				
				setTimeout(function(){
					Messenger.checkNewMessages();
				}, 10000);
		 
			}
		});
	},


	checkNewMessagesMobile: function(){

		console.log("START CHECK NEW MESSAGES MOBILE");

		var contactId = dialog ? dialog.contactId : 0;


		$.ajax({
			url: '/messenger/newMessagesMobile/' + Messenger.currentUserId + '/' + contactId,
			headers: { 'apiKey': Messenger.apiKey },
			//url: '/chat/index.php?checkNewMessages=true&userId='+Messenger.currentUserId,
			timeout:80000,
			dataType: 'json',
			//data: 'message='+message,
			context: this,
			error: function(response){
				console.log('ABORT CHECK NEW MESSAGES');
				console.log(JSON.stringify(response));
				//Messenger.checkNewMessages();
				//$('.error').html(response.responseText);
			},
			success: function(response, status) {
				if (response.newMessagesNumber > 0){
					$('.bottom_menu .item.chat .number div').text(response.newMessagesNumber);
					$('.bottom_menu .item.chat .number').show();
				}
				else{
					$('.bottom_menu .item.chat .number').hide()
				}
				setTimeout(function(){
					Messenger.checkNewMessagesMobile();
				}, 10000);
			}
		});
	},


	openChatsByActiveSessions: function(){
		
		
		$.ajax({
			url: '/messenger/activeChats/userId:' + Messenger.currentUserId,
			headers: { 'apiKey': Messenger.apiKey },
			//url: '/chat/index.php?getActiveChats=true&userId='+Messenger.currentUserId,
			timeout:80000,
			dataType: 'json',			
			context: this,
			error: function(response){				
				console.log(JSON.stringify(response));
				//$('.error').html(response.responseText);
			},
			success: function(response, status){
				console.log(JSON.stringify(response));
				var activeChats = response.activeChats;
				
				if(activeChats.length > 0){
					
					for(var i in activeChats){
						
						contact = activeChats[i];
						var options = {
							contactId: contact.id,
							contactName: contact.name,
							creatingInCycle: true
						};
						
						var chat = new Chat(options);
						chat.open();
						
					}
				}
				
				/*
				setTimeout(function(){
					Messenger.checkActiveWindowsNewMessages();  
				}, 200);
				*/
				
			}
		});
	},
	
	
	checkMessagesIfRead: function(){

        console.log("CHECK IF READ");
		
		if(Messenger.unreadMessagesIds.length > 0){

			
			if(Messenger.readMessagesRequest != '')
				Messenger.readMessagesRequest.abort();
			
			
			console.log('START ActiveWindowsReadMessages');	 		
			
			Messenger.readMessagesRequest = $.ajax({ 
				url: '/messenger/checkMessagesIfRead/userId:' + Messenger.currentUserId,
				headers: { 'apiKey': Messenger.apiKey },
				timeout:80000,
				dataType: 'json',
				type: 'Post',
				data: 'messages=' + Messenger.unreadMessagesIds,
				context: this,
				error: function(response){
					console.log('ABORT ActiveWindowsReadMessages');
					console.log(JSON.stringify(response));
					if(response.statusText != 'abort'){
						setTimeout(function(){
							console.log("SE Read");
							Messenger.checkMessagesIfRead();  
						}, 1000);
					}
					//$('.error').html(response.responseText);
				},
				success: function(response, status){				
					console.log(JSON.stringify(response));
					console.log('END ActiveWindowsReadMessages');
					if(response.readMessages.length > 0){
						for(var i in response.readMessages){
							var messageId = response.readMessages[i];
							/*
							$('#chatMessageSection_' + messageId).find('.status').html(Messenger.messageStatus.read);
							$('#dialogMessageSection_' + messageId).find('.status').html(Messenger.messageStatus.read);
							$('.chatMessageSection_' + messageId).find('.status').html(Messenger.messageStatus.read);
							$('.dialogMessageSection_' + messageId).find('.status').html(Messenger.messageStatus.read);
							*/

                            $('#chatMessageSection_' + messageId)
                                .removeClass('sent')
                                .removeClass('unread')
                                .addClass(Messenger.messageStatus.read)
                            ;

                            $('#dialogMessageSection_' + messageId)
                                .removeClass('sent')
                                .removeClass('unread')
                                .addClass(Messenger.messageStatus.read)
                            ;

                            $('.chatMessageSection_' + messageId)
                                .removeClass('sent')
                                .removeClass('unread')
                                .addClass(Messenger.messageStatus.read)
                            ;

                            $('.dialogMessageSection_' + messageId)
                                .removeClass('sent')
                                .removeClass('unread')
                                .addClass(Messenger.messageStatus.read)
                            ;

							Messenger.deleteUnreadMessageId(messageId);
						}						 
					}
					
					Messenger.checkMessagesIfRead();
					
				}
			});
		
		}
		else{
			setTimeout(function(){
				Messenger.checkMessagesIfRead();  
			}, 1000);
		}
	},
	
	deleteUnreadMessageId: function(messageId){
		var index = Messenger.unreadMessagesIds.indexOf(messageId);
		if (index > -1) {
			Messenger.unreadMessagesIds.splice(index, 1);
		}
	},
	
	checkActiveWindowsNewMessages: function(){
		
		if(Messenger.getAllChats().length > 0 || dialog){			
			
			if(Messenger.newMessagesRequest != '')
				Messenger.newMessagesRequest.abort();
			
			var checkForDialogAlso = false;
			var contactId = false;
			
			if(dialog){
				checkForDialogAlso = true;
				contactId = dialog.contactId;
				//Messenger.setUnreadMessagesIds(dialog);
			}
			/*
			for(var i in Messenger.chats){				
				var chat = Messenger.chats[i];				
				Messenger.setUnreadMessagesIds(chat);
			}
			*/
			
			console.log('START ActiveWindowsNewMessages');
			
			Messenger.newMessagesRequest = $.ajax({ 
				url: '/messenger/activeChats/newMessages/userId:' + Messenger.currentUserId + '/contactId:' + contactId + '/' + checkForDialogAlso,
				headers: { 'apiKey': Messenger.apiKey },
				timeout:80000,
				dataType: 'json',
				type: 'Post',
				//data: 'messages=' + Messenger.unreadMessagesIds,
				context: this,
				error: function(response){
					console.log('ABORT ActiveWindowsNewMessages');
					console.log(JSON.stringify(response));
					if(response.statusText != 'abort'){
						setTimeout(function(){
							console.log("SE New");
							Messenger.checkActiveWindowsNewMessages();  
						}, 1000);
					}
					//$('.error').html(response.responseText);
				},
				success: function(response, status){				
					console.log(JSON.stringify(response));
					console.log('END ActiveWindowsNewMessages');
									
					if(response.newMessages.length > 0){
						for(var i in response.newMessages){
							var message = response.newMessages[i];					
							var chat = Messenger.getChat(message.from);
													
							if(chat){
								
								//alert(dialog.contactId); 
								
								if(dialog && chat.contactId == dialog.contactId){									
									dialog.insertMessage(message);	
									if(dialog.allowedToReadMessage){
										dialog.setMessageAsRead(message);
									}
								}
								
								chat.insertMessage(message);
								
								if(chat.isMinimized){
									chat.addMessageToWaiting(message);									
									chat.blinkingStart();
									Messenger.blinkingTitleStart();									
									if(!Messenger.soundPlayed){
										$('body').append('<embed src="/assets/frontend/media/newMessage.mp3" autoplay="true" autostart="true" type="audio/x-wav" width="1" height="1">');
										Messenger.soundPlayed = true;
									}
								}else{								
									if(chat.allowedToReadMessage){
										chat.setMessageAsRead(message);
									}									
									chat.blinkingStop();
									Messenger.blinkingTitleStop();
								}								
							}							
							else if(dialog && message.from == dialog.contactId){
								//alert(dialog.contactId);
								Messenger.currentUserHasPoints = response.currentUserHasPoints;
								dialog.insertMessage(message);
								if(dialog.allowedToReadMessage) {
									dialog.setMessageAsRead(message);
								}
							}							
							else{
								console.log("This chat is not active.");
								var options = {
									contactId: message.from,
									contactName: message.userName,
									creatingInCycle: true
								};
									
								var chat = new Chat(options);
								chat.open();
							}
							
						}			
							
					}
					
					Messenger.soundPlayed = false;
					
					/*
					if(response.readMessages.length > 0){
						for(var i in response.readMessages){
							var messageId = response.readMessages[i];
							$('#chatMessageSection_' + messageId).find('.status').html(Messenger.messageStatus.read);
							$('#dialogMessageSection_' + messageId).find('.status').html(Messenger.messageStatus.read);
							$('.chatMessageSection_' + messageId).find('.status').html(Messenger.messageStatus.read);
							$('.dialogMessageSection_' + messageId).find('.status').html(Messenger.messageStatus.read);
						}
						 
					}
					*/
					Messenger.checkActiveWindowsNewMessages();
					
				}
			});
		
		}
	},


    setMessageAsNotified: function(messageId){

        console.log("SET AS READ REQUEST");

        $.ajax({
            url: '/messenger/message/notify/messageId:' + messageId + '/userId:' + Messenger.currentUserId,
            headers: { 'apiKey': Messenger.apiKey },
            timeout:10000,
            dataType: 'json',
            context: this,
            success: function(response, status){
                console.log("SET AS NOTIFIED RESPONSE");
                if(response.success){
                    console.log("SET AS NOTIFIED");
                }
            }
        });

    },
	
	setUnreadMessagesIds: function(chat){
		for(var j in chat.unreadMessagesFromUser){
			var message = chat.unreadMessagesFromUser[j];					
			if(message.id != undefined && Messenger.unreadMessagesIds.indexOf(message.id) == -1){
				Messenger.unreadMessagesIds.push(message.id);						
			}					
		}
	},
	
	addUnreadMessageId: function(message){
		if(!message.isRead && message.isSaved && Messenger.unreadMessagesIds.indexOf(message.id) == -1){
			Messenger.unreadMessagesIds.push(message.id);
		}	
	},
	
	useFreePointToReadMessage: function(thisObj){
		
		var messageSectionId = $(thisObj).parents('.messageSection').attr('id');
		var messageSectionIdArr = messageSectionId.split('_');
		var messageId = messageSectionIdArr[1];
		
		console.log("START USE");
			
		$.ajax({
			url: '/messenger/message/messageId:' + messageId + '/userId:' + Messenger.currentUserId + '/useFreePointToRead',
			headers: { 'apiKey': Messenger.apiKey },
			timeout:10000,
			dataType: 'json',
			error: function(error){
				console.log(JSON.stringify(error));
			},
			success: function(response){
				if(response.success){
					//console.log("MESSAGE:" + response.message);
					var message = $.emoticons.replace(response.message.text);
					var messageBox = $(thisObj).parents('.message');
					var dateTimeBlock = '<div class="timerdiv dateTime">' + messageBox.find('.dateTime').text() + '</div>';
					messageBox.css({'min-height': messageBox.height(), 'position':'relative'})
					messageBox.html('<h4>' + message + '</h4>' + dateTimeBlock);
					//messageBox.find('.dateTime').css({'position':'absolute', 'bottom':'0px'});
					var chat = Messenger.getChat(response.message.from);
					if(chat){						
						var currentScrollPosition = Math.abs(messageBox.parents('.overview').position().top);
						chat.scrollBarWraper.tinyscrollbar_update(currentScrollPosition);
					}					
				}
				else{
					alert("אין לך נקודות לקיאת הודעה");
				}
			}
		});
	},
	
	blinkingTitleStart: function(){	
		Messenger.blinkingTitleStop();
		blinkingTitle = setInterval(function(){
			document.title = (document.title == "***New Message***" ? Messenger.docTitle : "***New Message***");
		}, 500);
		
	},
	
	blinkingTitleStop: function(){
		if( blinkingTitle ){
			clearInterval(blinkingTitle);
			document.title = Messenger.docTitle;
		}
	}, 
	
	
	play: function(id){
		var sound = document.getElementById(id);		
		sound.Play();		
	},	
	
	createRandomId: function(){
	    var text = "";
	    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	    for( var i=0; i < 8; i++ )
	        text += possible.charAt(Math.floor(Math.random() * possible.length));

	    return text;
	},
	
	emoticonsBindClickEvent: function(){
		$('.emoticons_wrapper .emoticon').unbind('click').click(function(){
			Messenger.insertEmoticonCode($(this).text(), document.getElementById('dialog_textarea'));
		});
	},
	
	insertEmoticonCode: function(str, textArea){
		var val = textArea.value;
		var before = val.substring(0, textArea.selectionStart);
		var after = val.substring(textArea.selectionEnd, val.length);
		textArea.value = before + str + after;
		Messenger.setCursor(textArea, before.length + str.length);
	},
	
	setCursor: function(elem, pos) {
	   if (elem.setSelectionRange) {
	      elem.focus();
	      elem.setSelectionRange(pos, pos);
	   } else if (elem.createTextRange) {
	      var range = elem.createTextRange();
	      range.collapse(true);
	      range.moveEnd('character', pos);
	      range.moveStart('character', pos);
	      range.select();
	   }
	},
	
	qtipInit: function(object,template, showCallbackFunc, title){
		object.qtip({
			events: {
				show: showCallbackFunc,
			},
			content: {
				text: template.html(),
				title: {
					text: title,
					button: true
				}
			},
			style: {
				classes: 'ui-tooltip-shadow ui-tooltip-rounded qtip-bootstrap',
				tip: {
		            corner: true,
		            height: 24
		        }
			},
			position: {
				my: 'top center', // Use the corner...
				at: 'bottom center', // ...and opposite corner
				width: 480,
				adjust: {
					y: -210
			    },
			},
			show: {
				event: 'click'
			},
			hide: false,
	    });
	}
	
};






