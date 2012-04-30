/**
 * Interaction for the mailmotor
 *
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.profiles =
{
	init: function()
	{
		jsBackend.profiles.addToGroup.init();

		jsBackend.profiles.showWhenEnum.init();
	},

	addToGroup:
	{
		init: function()
		{
			// update the hidden input for the new group's ID with the remembered value
			var $txtNewGroup = $('input[name="newGroup"]').val(window.name);

			// clone the groups SELECT into the "add to group" mass action dialog
			$('#massAddToGroupListPlaceholder').replaceWith(
				$('select[name="group"]')
					.clone(true)
					.removeAttr('id')
					.attr('name', 'newGroup')
					.css('width', '90%')
					.on('change', function()
					{
						// update the hidden input for the new group's ID with the current value
						$txtNewGroup.val(this.value);

						// remember the last selected value for the current window
						window.name = this.value;
					})
					.val(window.name)
			);
		}
	},

	showWhenEnum:
	{
		init: function()
		{
			$dropdown = $('.showValues');
			if($dropdown.length = 1)
			{
				if($dropdown.val() == 'Enum') $('#showWhenEnum').removeClass();
				else $('#showWhenEnum').removeClass().addClass('hidden');

				$dropdown.on('change', function(){
					if($dropdown.val() == 'Enum') $('#showWhenEnum').removeClass();
					else $('#showWhenEnum').removeClass().addClass('hidden');
				});
			}
		}
	}
}

$(jsBackend.profiles.init);