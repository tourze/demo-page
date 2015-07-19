<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Edit User') ?></legend>
			<?php include Kohana::find_file('views', 'page/error') ?>

			<div class="control-group">
				<label class="control-label" for="username"><?php echo __('User Name') ?></label>
				<div class="controls">
					<input name="username" type="text" id="username" class="input-xxlarge" value="<?php echo $user->username ?>" />
					<span class="help-block"><?php echo __('Enter user\'s login name.') ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="password"><?php echo __('Password') ?></label>
				<div class="controls">
					<input name="password" type="password" id="password" class="input-xxlarge" value="" />
					<span class="help-block">ffffff</span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="password_confirm"><?php echo __('Repeat Password') ?></label>
				<div class="controls">
					<input name="password_confirm" type="password" id="password_confirm" class="input-xxlarge" value="" />
					<span class="help-block">ffffff</span>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button class="btn btn-primary" type="submit"><?php echo __('Save Changes') ?></button>
					<a class="btn" href="<?php echo Route::url('auth-admin', array('controller' => 'User', 'action' => 'new')) ?>"><?php echo __('Cancel') ?></a>
				</div>
			</div>
		</form>
	</div>

	<div class="span3">
		<div class="box">
			<h1><?php echo __('Help') ?></h1>
			<p>Help goes here</p>
		</div>
	</div>
</div>
