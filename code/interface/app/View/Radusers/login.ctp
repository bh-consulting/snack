<div class="form form-signin well">
    <?php echo $this->Session->flash('auth'); ?>
    <?php echo $this->Form->create('Raduser'); ?>

    <fieldset>
        <legend>
<?php
echo __('Please enter your username and password');
?>
        </legend>
<?
echo $this->Form->input(
    'username',
    array(
        'label' => __('Username'),
        'id' => 'username_field',
        'autocomplete' => 'off',
    )
);
echo $this->Form->input(
    'passwd',
    array(
        'label' => __('Password'),
        'autocomplete' => 'off',
    )
);
?>
    </fieldset>

    <? echo $this->Form->end(__('Sign in')); ?>
</div>

<?php $this->start('script'); ?>
<script type="text/javascript">
//$('#username_field').focus();
$(document).ready(function(){
    $('#username_field').focus();
});
</script>
<?php $this->end(); ?>
