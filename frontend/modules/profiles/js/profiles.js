/**
 * Interaction for the profiles module
 *
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 * @author	Wouter Sioen <wouter.sioen@gmail.com>
 */
jsFrontend.profiles = {
	/**
	 * Kind of constructor
	 */
	init: function()
	{
		jsFrontend.profiles.showPassword();

		jsFrontend.profiles.autoSuggest();

		jsFrontend.profiles.loadMessages();

		jsFrontend.profiles.loadThreads();

		jsFrontend.profiles.markRead();

		jsFrontend.profiles.dropdown();

		jsFrontend.profiles.addComment();

		jsFrontend.profiles.removeActivity();

		jsFrontend.profiles.removeActivityComment();

		jsFrontend.profiles.report();

		jsFrontend.profiles.loadProfiles();
	},

	/**
	 * Make possible to show passwords in clear text
	 */
	showPassword: function()
	{
		// checkbox showPassword is clicked
		$('#showPassword').on('click', function()
		{
			// checkbox is checked
			if($(this).is(':checked'))
			{
				// clone password and change type
				$('.showPasswordInput').clone().attr('type', 'input').insertAfter($('.showPasswordInput'));

				// remove original
				$('.showPasswordInput:first').remove();
			}

			// checkbox not checked
			else
			{
				// clone password and change type
				$('.showPasswordInput').clone().attr('type', 'password').insertAfter($('.showPasswordInput'));

				// remove original
				$('.showPasswordInput:first').remove();
			}
		});
	},

	/**
	 * Makes the profile dropdown work
	 */
	dropdown: function()
	{
		$('#openProfilesDropdown span').on('click', function(e){
			e.preventDefault();
			$('#ddProfiles').slideToggle(250);
		});
	},

	/**
	 * Autosuggests users in the messaging system
	 */
	autoSuggest: function()
	{
		// grab element
		var $input = $('input.profilesAutoSuggest');

		if($input.length > 0)
		{
			function split(val) {
				return val.split(/;\s*/);
			}
			function extractLast(term) {
				return split(term).pop();
			}
	
			// search widget suggestions
			$input.autocomplete(
			{
				minLength: 1,
				source: function(request, response)
				{
					// ajax call
					$.ajax(
					{
						data:
						{
							fork: { module: 'profiles', action: 'autosuggest' },
							term: extractLast(request.term)
						},
						success: function(data, textStatus)
						{
							// init var
							var realData = [];
	
							// alert the user
							if(data.code != 200 && jsFrontend.debug) { alert(data.message); }
	
							if(data.code == 200)
							{
								for(var i in data.data) realData.push({ first_name: data.data[i].first_name, last_name: data.data[i].last_name, display_name: data.data[i].display_name, url: data.data[i].url });
							}
	
							// set response
							response(realData);
						}
					});
				},
				select: function(event, ui)
				{
					if($(this).hasClass('redirect'))
					{
						window.location.href = ui.item.url;
					}
					else
					{
						// add the selected item after the previously added items
						var terms = split($(this).val());
						terms.pop();
						terms.push(ui.item.display_name);
						terms.push("");
						$(this).val(terms.join("; "));
						return false;
					}
				}
			})
			// and also: alter the autocomplete style: add description!
			.data('autocomplete')._renderItem = function(ul, item)
			{
				return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><strong>' + item.display_name + '</strong><br \>' + item.first_name + ' ' + item.last_name + '</a>' )
				.appendTo(ul);
			};
		}
	},

	/**
	 * Loads more messages
	 */
	loadMessages: function()
	{
		// grab element
		var $button = $('#loadMessages');

		if($button.length > 0)
		{
			$button.on('click',function()
			{
				$threadId = $('#messages').attr("class");
				$offset = parseInt($button.attr("class"));
				$.ajax(
				{
					data:
					{
						fork: { module: 'profiles', action: 'load_messages' },
						offset: $offset,
						threadId: $threadId
					},
					success: function(data, textStatus){
						$offset += 4;
						// remove the load messages button if there are no more messages
						if(data.data['amount'] < $offset) $button.remove();
						else $button.removeClass().addClass($offset + "");

						// add the loaded messages to the DOM
						for(var i in data.data)
						{
							if(i != "amount")
							{
								// add avatar part
								$html = '<div class="messageHolder clearfix" style="display:none"><div class="imageHolder"><img src="{$FRONTEND_FILES_URL}';
								if(data.data[i].avatar) $html += '/profiles/avatars/64x64/' + data.data[i].avatar + '" alt="" ';
								else
								{
									$html += '/layout/images/default_author_avatar.gif" ';
									if(data.data[i].facebook_id) $html += ' alt="' + data.data[i].display_name + '" class="replaceWithFacebook" data-facebook-id="' + data.data[i].facebook_id + '" ';
								}
								$html += 'width="64" height="64" /></div>';
								// message part
								$html += '<div class="messageContent"><header class="hd"><h4><a href="{$var|geturlforblock:'profiles'}/' + data.data[i].url + '">' + data.data[i].display_name;
								$html += '</a></h4><ul><li>' + data.data[i].created_on + '</li></ul></header><p>' + data.data[i].text + '</p></div></div>';

								$('.bd.messages').prepend($html);
								$('.bd.messages .messageHolder').slideDown(250);
							}
						}
					}
				});
			});
		}
	},

	/**
	 * Loads more threads
	 */
	loadThreads: function()
	{
		// grab element
		var $button = $('#loadThreads');

		if($button.length > 0)
		{
			$button.on('click',function()
			{
				$userId = $('#messages').attr("class");
				$offset = parseInt($button.attr("class"));
				$.ajax(
				{
					data:
					{
						fork: { module: 'profiles', action: 'load_threads' },
						offset: $offset,
						userId: $userId
					},
					success: function(data, textStatus)
					{
						$offset += 4;
						// remove the load messages button if there are no more messages
						if(data.data['amount'] < $offset) $button.remove();
						else $button.removeClass().addClass($offset + "");

						// add the loaded messages to the DOM
						for(var i in data.data)
						{
							if(i != "amount")
							{
								// create html
								$html = '<div class="thread" style="display:none;"><header class="hd"><h4';
								if(data.data[i].status == 0) $html += ' class="unread"';
								$html += '><a href="{$var|geturlforblock:'profiles':'message_detail'}/' + data.data[i].id + '">';
								// loop through receivers
								for(var j in data.data[i].receivers)
								{
									$html += data.data[i].receivers[j].display_name + ', ';
								}
								// remove last two characters
								$html = $html.substring(0, $html.length-2);
								$html += '</a></h4><ul><li>' + data.data[i].created_on + '</li></ul></header><p>' + data.data[i].text + '</p>';
								if(data.data[i].status == 0) $html += '<p class="ajaxLink"><span class="markRead" id="' + data.data[i].id + '>{$lblMarkAsRead}</span></p>';
								$html += '</div>';

								// give them a nice animation
								$('.bd.threads').append($html);
								$('.bd.threads .thread').slideDown(250);
							}
						}
					}
				});
			});
		}
	},

	/**
	 * Marks a thread as read
	 */
	markRead: function()
	{
		// grab element(s)
		var $button = $('.markRead');

		if($button.length > 0)
		{
			$button.on('click',function()
			{
				$threadId = $(this).attr('id');
				$userId = $('#messages').attr("class");
				$.ajax(
				{
					data:
					{
						fork: { module: 'profiles', action: 'mark_as_read' },
						threadId: $threadId,
						userId: $userId
					},
					success: function(data, textStatus)
					{
						$('.' + $threadId + ' h4').removeClass('unread');
						$('.markRead#' + $threadId).parent().remove();
					}
				});
			});
		}
	},

	/**
	 * Ajax action to add comments on an activity stream item
	 */
	addComment: function()
	{
		// grab element(s)
		$button = $('.addComment');

		if($button.length > 0)
		{
			$button.on('click', function()
			{
				// get data
				$activity_id = $(this).parent().parent().parent().attr('id');
				$user_id = $('#profileId').html();
				$commentText = $(this).parent().find('.inputText').val();
				$divToAdd = $(this).parent().parent();
				$divToRemove = $(this).parent();

				$.ajax(
				{
					data:
					{
						fork: { module: 'profiles', action: 'add_comment'},
						activity: $activity_id,
						userId: $user_id,
						text: $commentText
					},
					success: function(data, textStatus)
					{
						console.log(data.data);
						// add avatar part
						$html = '<div class="messageHolder clearfix" id="comment-' + data.data.id + '" style="display:none"><span class="deleteCommentButton"></span><div class="imageHolder"><img src="{$FRONTEND_FILES_URL}';
						if(data.data.avatar) $html += '/profiles/avatars/64x64/' + data.data.avatar + '" alt="" ';
						else
						{
							$html += '/backend_users/avatars/64x64/no-avatar.gif" ';
							if(data.data.facebook_id) $html += ' alt="' + data.data.url + '" class="replaceWithFacebook" data-facebook-id="' + data.fdata.acebook_id + '" ';
						}
						$html += 'width="48" height="48" /></div>';
						// add text
						$html += '<div class="messageContent"><p><a href="{$var|geturlforblock:'profiles'}/' + data.data.url + '">' + data.data.first_name + ' ' + data.data.last_name;
						$html += '</a> - {$lblAddedJustNow}</p><p>' + $commentText + '</p></div></div>';

						$divToRemove.remove();
						$divToAdd.append($html);
						$('.messageHolder').slideDown(250);

						// reinitialize the removecomment part
						jsFrontend.profiles.removeActivityComment();
					}
				});
			});
		}
	},

	/**
	 * Ajax action to remove an activity
	 */
	removeActivity: function()
	{
		// grab element(s)
		$button = $('.deleteButton');

		if($button.length > 0)
		{
			$button.on('click', function()
			{
				// get data
				$activity_id = $(this).parent().attr('id');
				$divToRemove = $(this).parent();

				$.ajax(
				{
					data:
					{
						fork: { module: 'profiles', action: 'remove_activity'},
						activityId: $activity_id,
					},
					success: function(data, textStatus)
					{
						// remove activity
						$divToRemove.remove();
					}
				});
			});
		}
	},

	/**
	 * Ajax action to remove a comment from an activity stream
	 */
	removeActivityComment: function()
	{
		// grab element(s)
		$button = $('.deleteCommentButton');

		if($button.length > 0)
		{
			$button.on('click', function()
			{
				// get data
				$comment_id = $(this).parent().attr('id').replace('comment-', '');
				$divToRemove = $(this).parent();

				$.ajax(
				{
					data:
					{
						fork: { module: 'profiles', action: 'remove_activity_comment'},
						commentId: $comment_id,
					},
					success: function(data, textStatus)
					{
						// remove activity
						$divToRemove.remove();
					}
				});
			});
		}
	},

	/**
	 * Ajax action to report comments as inapropriate
	 */
	report: function()
	{
		// grab element(s)
		$button = $('.report');

		if($button.length > 0)
		{
			$button.on('click', function()
			{
				// get data
				$comment_id = $(this).parent().attr('id').replace('comment-', '');
				$divToRemove = $(this).parent();

				$.ajax(
				{
					data:
					{
						fork: { module: 'profiles', action: 'report_comment'},
						commentId: $comment_id,
					},
					success: function(data, textStatus)
					{
						// show message
						$divToRemove.parent().prepend('<div class="message warning"><p>{$msgReported}</p></div>');
					}
				});
			});
		}
	},

	/**
	 * Ajax actions to load more profiles
	 */
	loadProfiles: function()
	{
		// grab element
		$button = $('#loadProfiles');

		if($button.length > 0)
		{
			$button.on('click', function(e)
			{
				// prevent scrolling
				e.preventDefault();

				// get data
				$offset = $('.profilePreview').size()
				$limit = 15;
				// get lettre out of url
				$results = new RegExp('[\\?&]lettre=([^&#]*)').exec(window.location.href);
				$lettre = $results[1] || 0;

				$.ajax(
				{
					data:
					{
						fork: { module: 'profiles', action: 'load_profiles'},
						offset: $offset,
						limit: $limit,
						lettre: $lettre
					},
					success: function(data, textStatus)
					{
						if(data.data['amount'] < $offset + $limit) $button.remove();

						// add the loaded profiles to the DOM
						for(var i in data.data)
						{
							if(i != "amount")
							{
								// create html
								$html = '<div class="profilePreview" style="display: none;"><img src="{$FRONTEND_FILES_URL}';

								if(data.data[i].avatar) $html += '/profiles/avatars/64x64/' + data.data[i].avatar + '" alt="" ';
								else
								{
									$html += '/layout/images/default_author_avatar.gif" ';
									if(data.data[i].facebook_id) $html += ' alt="' + data.data[i].display_name + '" class="replaceWithFacebook" data-facebook-id="' + data.data[i].facebook_id + '" ';
								}

								$html += 'width="64" height="64" class="avatar" /><a href="{$var|geturlforblock:'profiles'}/' + data.data[i].url + '">' + data.data[i].first_name + ' ' + data.data[i].last_name + '</a></div>';

								// give them a nice animation
								$('.profiles').append($html);
								$('.profiles .profilePreview').slideDown(250);
							}
						}
					}
				});
			});
		}
	}
}

$(jsFrontend.profiles.init);