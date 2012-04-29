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

		jsFrontend.profiles.dropdown();
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
		$('#openProfilesDropdown').on('click', function(){
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
			$button.on('click',function(){
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
						console.log($offset);
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
								$html += '<div class="messageContent"><header class="hd"><h4><a href="{$var|geturlforblock:'profiles'}/{$thread.url}">' + data.data[i].display_name;
								$html += '</a></h4><ul><li>' + data.data[i].created_on + '</li></ul></header><p>' + data.data[i].text + '</p></div></div>';

								$('.bd.messages').prepend($html);
								$('.bd.messages .messageHolder').slideDown(250);
							}
						}
					}
				});
			});
		}
	}
}

$(jsFrontend.profiles.init);