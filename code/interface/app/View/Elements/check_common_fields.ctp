<?
echo $this->Form->input('comment');
echo $this->Form->input('expiration_date', array('label' => __('Expiration date'), 'class' => 'datetimepicker'));
echo $this->Form->input('simultaneous_use', array('label' => __('Simultaneous Use')));
?>
