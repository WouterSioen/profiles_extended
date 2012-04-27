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
	 * Autosuggests users in the messaging system
	 */
	autoSuggest: function()
	{
		// grab element
		var $input = $('input.profilesAutoSuggest');

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
}

$(jsFrontend.profiles.init);