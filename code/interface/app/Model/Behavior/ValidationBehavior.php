<?php
App::uses('ModelBehavior', 'Model');

class ValidationBehavior extends ModelBehavior {

    /**
     * Check if field is not empty while adding/updating cisco or md5 user.
     */
    public static function notEmptyIfCiscoOrLoginpass($model, $field = array(),
        $was_cisco = null) {

        $data = &$model->data[$model->name];

        $value = array_shift($field);
        $was_cisco = isset($data[$was_cisco]) ? $data[$was_cisco] : false;

        // NEW raduser (no id set)
        if (!isset($data['id'])) {
            if (empty($value) 
                && ($data['is_cisco'] == 1
                || $data['is_loginpass'] == 1)
            ) {
                return false;
            }
        // UPDATE raduser (id isset)
        } else {
            if(isset($data['is_cisco'])){
                if(empty($value)
                    && !$was_cisco
                    && $data['is_cisco']
                    && !(isset($data['is_loginpass'])
                        && $data['is_loginpass'])
                ){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if value is unique in the specified column.
     */
    public static function isUniqueValue($model, $field = array(),
        $column = null, $type = 'text') {

        if (is_null($column) || is_array($column) || is_array($type)) {
            return false;
        }
        switch ($type) {
        case 'mac':
            $value = preg_replace('#:|-#', '', array_shift($field));
            break;
        default:
            $value = array_shift($field);
        }

        $count = $model->find(
            'count',
            array(
                'conditions' => array(
                    $column => $value,
                ),
            )
        );

        return $count === 0;
    }

    /**
     * Check if fields are formatted like a MAC address.
     */
    public static function isMACFormat($model, $field = array()) {
        return Utils::isMAC(array_shift($field));
    }

    /**
     * Check if field is equal to the otherField.
     */
    public static function equalValues($model, $field = array(), $otherField) {
        $data = &$model->data[$model->name];

        if (is_array($otherField)
            || !isset($data[$otherField])
        ) {
            return false;
        }

        return array_shift($field) === $data[$otherField];
    } 
}
?>
