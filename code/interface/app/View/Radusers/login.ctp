<div class="form well">
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

<script type="text/javascript">
$(function(){
    $('#username_field').focus();
});
</script>
