{option:sentMessage}
	<div class="message success"><p>{$msgMessageSent}</p></div>
{/option:sentMessage}

<section id="messages" class="mod">
	<div class="inner">
		<div class="bd">
			<p>
				<a href="{$var|geturlforblock:'profiles':'messages'}">{$lblBackToOverview}</a>
			</p>
			{option:thread}
				{iteration:thread}
					<header class="hd">
						<h4><a href="{$var|geturlforblock:'profiles'}/{$thread.url}">{$thread.display_name}</a></h4>
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
			<p>
				<a href="{$var|geturlforblock:'profiles':'messages'}">{$lblBackToOverview}</a>
			</p>
		</div>
	</div>
</section>