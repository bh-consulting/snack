<div class="form well">
    <? echo $this->Session->flash('auth'); ?>
    <? echo $this->Form->create('Raduser'); ?>
    <fieldset>
        <legend><? echo __('Please enter your username and password'); ?></legend>
        <?
        echo $this->Form->input('username', array('label' => __('Username'), 'id' => 'username_field'));
        echo $this->Form->input('passwd', array('label' => __('Password')));
        ?>
    </fieldset>
    <? echo $this->Form->end(__('Sign in')); ?>
</div>

<script type="text/javascript">
    $(function(){
        $('#username_field').focus();
    });
</script>