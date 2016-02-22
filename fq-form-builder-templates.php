<?php


$templates['form_error_message'] = '

	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>OOPS!</strong> There are errors below...
	</div>

';
	
$templates['form_success_message'] = '

	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>Thanks!</strong> There are no errors.
	</div>

';

$templates['form_save_message'] = '

	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>Thanks!</strong> Your message has been saved.
	</div>

';

$templates['form_email_message'] = '

	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>Thanks!</strong> Your message was sent...
	</div>

';




$templates['form'] = '

	<div>
		<h2>{FORM_TITLE}</h2>
		<form class="{CLASS}" action="{ACTION}" method="{METHOD}" id="{ID}" enctype="{ENCTYPE}">
			<fieldset>
			
				{FORM_ELEMENTS}

			</fieldset>
		</form>
		<hr>
	</div>

';

$templates['text'] = '

	<!-- text element -->	
	<div class="form-group">
		<label for="{NAME}">{LABEL}</label>
		<input type="{TYPE}" class="form-control {CLASS}" id="{ID}" name="{NAME}" placeholder="{PLACEHOLDER}" value="{VALUE}" />
		<span class="help-block"><b class="text-danger">{ERROR}</b> {HELP}</span>
	</div>

';

$templates['text-kooky'] = '

	<!-- text element -->	
	<div class="form-group">
		<label for="{NAME}">KOOKY! {LABEL}</label>
		<input type="{TYPE}" class="form-control {CLASS}" id="{ID}" name="{NAME}" placeholder="{PLACEHOLDER}" value="{VALUE}" />
		<span class="help-block"><b class="text-danger">{ERROR}</b> {HELP}</span>
	</div>

';

$templates['textarea'] = '

	<!-- textarea element -->	
	<div class="form-group">
		<label for="{NAME}">{LABEL}</label>
		<textarea class="form-control {CLASS}" id="{ID}" name="{NAME}" placeholder="{PLACEHOLDER}" rows="{ROWS}">{VALUE}</textarea>
		<span class="help-block"><b class="text-danger">{ERROR}</b> {HELP}</span>
	</div>

';

$templates['submit'] = '

	<!-- button element -->	
	<button id="{ID}" name="{NAME}" value="{LABEL}" type="{TYPE}" class="btn btn-default">{LABEL}</button>

';

$templates['checkbox'] = '

	<!-- checkbox element -->	
		<label>{LABEL}</label>
		{REPEAT}
		<div class="checkbox">
			<label>
				<input type="{TYPE}" id="{ID}" name="{NAME}" value={VALUE} /> {LABEL}
			</label>
		</div>
		{REPEAT}
		<span class="help-block"><b class="text-danger">{ERROR}</b> {HELP}</span>

';


$templates['radio'] = '

	<!-- radio element -->	
		<label>{LABEL}</label>
		{REPEAT}
		<div class="radio">
			<label>
				<input type="{TYPE}" id="{ID}" name="{NAME}" value={VALUE} /> {LABEL}
			</label>
		</div>
		{REPEAT}
		<span class="help-block"><b class="text-danger">{ERROR}</b> {HELP}</span>

';


$templates['select'] = '

	<!-- select element -->	
	<div class="form-group">
		<label for="{NAME}">{LABEL}</label>
		<select id="{ID}" name="{NAME}" class="form-control {CLASS}">
			{REPEAT}<option value="{VALUE}">{LABEL}</option>{REPEAT}
		</select>
		<span class="help-block"><b class="text-danger">{ERROR}</b> {HELP}</span>
	</div>

';




$templates['html'] = '

	<!-- html element -->	
	<div class="form-group">
		{HTML}
	</div>

';


$templates['raw'] = '{HTML}';





return;









$templates['glyphicon'] = '

		<!-- text element -->	
		<div class="form-group">
			<div class="col-md-12">
				<div class="input-group">
					<span class="input-group-addon"><i class="glyphicon glyphicon-{GLYPHICON}"></i></span> 
					<input id="{ID}" name="{NAME}" placeholder="{PLACEHOLDER}" class="form-control {CLASS}" type="{TYPE}" value="{VALUE}" />
					{ERROR_MESSAGE}
				</div>
			</div>
		</div>

';


$templates['textarea_glyphicon'] = '

	<!-- text element -->	
	<div class="form-group">
		<div class="col-md-12">
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-{GLYPHICON}"></i></span> 
				<textarea rows="{ROWS}" id="{ID}" name="{NAME}" class="form-control {CLASS}" placeholder="{PLACEHOLDER}">{VALUE}</textarea>
				{ERROR_MESSAGE}
			</div>
		</div>
	</div>

';

