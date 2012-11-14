<h1>Add user</h1>
<?php
echo $this->Form->create('Radcheck', array("class" => "form-horizontal"));
?>
<div class="control-group">
    <label for="RadcheckUsername" class="control-label">Username</label>
    <? echo $this->Form->input('username', array('label' => false, 'div' => 'controls')); ?>
</div>

<div class="control-group">
    <label for="RadcheckAttribute" class="control-label">Attribute</label>
    <? echo $this->Form->input('attribute', array('label' => false, 'div' => 'controls')); ?>
</div>

<div class="control-group">
    <label for="RadcheckOp" class="control-label">Op</label>
    <? echo $this->Form->input('op', array('label' => false, 'div' => 'controls')); ?>
</div>

<div class="control-group">
    <label for="RadcheckValue" class="control-label">Value</label>
    <? echo $this->Form->input('value', array('label' => false, 'div' => 'controls')); ?>
</div>
    <div class="control-group">
        <div class="controls">
    <? echo $this->Form->end('Save user', array('div' => array('class' => 'btn'))); ?>
            </div>
        </div>
