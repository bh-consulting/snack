<div class="radusers form">
    <? echo $this->Session->flash('auth'); ?>
    <? echo $this->Form->create('Raduser'); ?>
    <fieldset>
        <legend><? echo __('Please enter your username and password'); ?></legend>
        <?
        echo $this->Form->input('username');
        echo $this->Form->input('value');
        ?>
    </fieldset>
    <? echo $this->Form->end(__('Login')); ?>
</div>
