<?if ($this->session->flashdata('error')):?>
	<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">×</button>
		<?=$this->session->flashdata('error')?>
	</div>
<?endif;?>

<?if ($this->session->flashdata('success')):?>
	<div class="alert alert-info">
	<button type="button" class="close" data-dismiss="alert">×</button>
		<?=$this->session->flashdata('success')?>
	</div>
<?endif;?>

<?if ($this->session->flashdata('warning')):?>
	<div class="alert alert-warning">
	<button type="button" class="close" data-dismiss="alert">×</button>
		<?=$this->session->flashdata('warning')?>
	</div>
<?endif;?>

<?if ($this->session->flashdata('msg')):?>
	<div class="alert alert-info">
	<button type="button" class="close" data-dismiss="alert">×</button>
		<?=$this->session->flashdata('msg')?>
	</div>
<?endif;?>
