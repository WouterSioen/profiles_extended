{option:sentMessage}
	<div class="message success"><p>{$msgMessageSent}</p></div>
{/option:sentMessage}

<section id="messages" class="mod">
	<div class="inner">
		<div class="bd">
			{option:thread}
				{iteration:thread}
					<header class="hd">
						<h4><a href="{$var|geturlforblock:'profiles'}/{$thread.display_name}">{$thread.first_name} {$thread.last_name}</a></h4>
						<ul>
							<li>{$thread.created_on}</li>
						</ul>
					</header>
					<p>
						{$thread.text}
					</p>
				{/iteration:thread}
			{/option:thread}
		</div>
	</div>
</section>
<section id="messageForm" class="mod">
	<div class="inner">
		<div class="bd">
			{form:message}
				<p{option:txtMessageError} class="errorArea"{/option:txtMessageError}>
					<label for="message">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtMessage}{$txtMessageError}
				</p>
				<p>
					<input class="inputSubmit" type="submit" value="{$lblSend|ucfirst}" />
				</p>
			{/form:message}
		</div>
	</div>
</section>