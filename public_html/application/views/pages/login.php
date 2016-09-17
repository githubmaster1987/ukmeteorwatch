<div class="row">

	<?php echo form_open('login', array('class' => 'form form-signin form-horizontal')); ?>

	<h2 class="form-signin-heading">Please sign in</h2>


	<?php if (validation_errors()): ?>
		<div class="alert alert-error fade in">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php echo validation_errors(); ?>
		</div>
	<?php endif; ?>


	<div class="control-group">
		<label class="control-label" for="title">Username:</label>
		<div class="controls">
			<?php echo form_input(array('name' => 'username', 'type' => 'text', 'id' => 'username', 'class' => 'input-block-level', 'required' => 'required', 'placeholder' => 'username'), set_value('username')) ?>
		</div>
	</div>

	<div class="control-group zero-bottom-margin">
		<label class="control-label" for="title">Password:</label>
		<div class="controls">
			<?php echo form_input(array('name' => 'password', 'type' => 'password', 'id' => 'password', 'class' => 'input-block-level', 'required' => 'required', 'placeholder' => 'password'), set_value('password')) ?>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<label class="checkbox">
				<input type="checkbox" id="remember" name="remember" value="1" <?php echo set_checkbox('remember', '1') ?> /> Remember me
			</label>
			<button class="btn btn-large btn-primary" type="submit">Sign in</button>

			<!--<p style="margin-top: 15px"><?php echo anchor('forgot', 'Forgot your password?', array('class' => '')); ?></p>-->
		</div>
	</div>



	<?php echo form_close(); ?>

</div>