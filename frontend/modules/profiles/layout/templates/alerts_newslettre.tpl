{* Success *}
{option:!updatePasswordHasFormError}
	{option:updateSuccess}
		<div class="message success"><p>{$msgUpdateSuccess}</p></div>
	{/option:updateSuccess}
{/option:!updatePasswordHasFormError}

{* Error *}
{option:updateHasFormError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:updateHasFormError}

{option:nonactive}
	<div class="message error"><p>{$errNonActiveError}</p></div>
{/option:nonactive}

<section id="updateAlertForm" class="mod">
	<div class="inner">
		<div class="bd">
			{form:updateAlert}
				<fieldset>
					<legend>{$lblAlerts|ucfirst}</legend>
					<p>
							<label for="alerts">{$chkAlerts} {$lblAlerts|ucfirst} </label>
						</p>
				</fieldset>
				<fieldset>
					<legend>{$lblNewslettre|ucfirst}</legend>
					<p>
						<label for="newslettre">{$chkNewslettre} {$lblNewslettre|ucfirst} </label>
					</p>
					<p>
						<input class="inputSubmit" type="submit" value="{$lblSave|ucfirst}" />
					</p>
				</fieldset>
			{/form:updateAlert}
		</div>
	</div>
</section>