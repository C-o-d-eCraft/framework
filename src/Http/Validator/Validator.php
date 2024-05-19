<?php

namespace Craft\Http\Validator;

use Craft\Contracts\RequestInterface;
use Craft\Http\Exceptions\HttpException;

class Validator
{
    /**
     * @var string
     */
    const ERROR_DEFAULT = 'Invalid';

    /**
     * @var array
     * Содержит данные из реквеста, а именно тело
     */
    protected $_fields = array();

    /**
     * @var array
     */
    protected $_errors = array();

    /**
     * @var array
     */
    protected $_validations = array();

    /**
     * @var array
     */
    protected $_labels = array();

    /**
     * Contains all rules that are available to the current valitron instance.
     *
     * @var array
     */
    protected $_instanceRules = array();

    /**
     * Contains all rule messages that are available to the current valitron
     * instance
     *
     * @var array
     */
    protected $_instanceRuleMessage = array();

    /**
     * @var array
     */
    protected static $_rules = array();

    /**
     * @var array
     * Хранит текст для ошибок валидации, ключи - правила валидации
     */
    protected static $_ruleMessages = array();

    /**
     * @var bool
     */
    protected $stop_on_first_fail = false;

    /**
     * @var bool
     */
    protected $prepend_labels = true;

    /**
     * Setup validation
     *
     * @param  array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(private RequestInterface $request)
    {
        $this->_fields = $this->request->getBodyContents();

        if (ValidationMessage::getMessage() !== null) {
            static::$_ruleMessages =  ValidationMessage::getMessage();
        } else {
            throw new \InvalidArgumentException("Не найден файл с описанием правил '" . ValidationMessage::class . "'");
        }
    }

    /**
     * @param bool $prepend_labels
     */
    public function setPrependLabels($prepend_labels = true)
    {
        $this->prepend_labels = $prepend_labels;
    }

    /**
     * Required field validator
     * Обзателен для заполнения
     *
     * @param  string $field
     * @param  mixed $value
     * @param  array $params
     * @return bool
     */
    protected function validateRequired($field, $value, $params = array())
    {
        if (isset($params[0]) && (bool)$params[0]) {
            $find = $this->getPart($this->_fields, explode('.', $field), true);
            return $find[1];
        }

        if (is_null($value) || (is_string($value) && trim($value) === '')) {
            return false;
        }

        return true;
    }

    /**
     * Validate that a field was "accepted" (based on PHP's string evaluation rules)
     *
     * This validation rule implies the field is "required"
     *
     * @param  string $field
     * @param  mixed $value
     * @return bool
     */
    protected function validateAccepted($field, $value)
    {
        $acceptable = array('yes', 'on', 1, '1', true);

        return $this->validateRequired($field, $value) && in_array($value, $acceptable, true);
    }

    /**
     * массив?
     *
     * @param  string $field
     * @param  mixed $value
     * @return bool
     */
    protected function validateArray($field, $value)
    {
        return is_array($value);
    }

    /**
     * число?
     *
     * @param  string $field
     * @param  mixed $value
     * @return bool
     */
    protected function validateNumeric($field, $value)
    {
        return is_numeric($value);
    }

    /**
     * Validate that a field is an integer
     *
     * @param  string $field
     * @param  mixed $value
     * @param  array $params
     * @return bool
     */
    protected function validateInteger($field, $value, $params)
    {
        if (isset($params[0]) && (bool)$params[0]) {
            //strict mode
            return preg_match('/^([0-9]|-[1-9]|-?[1-9][0-9]*)$/i', $value);
        }

        return filter_var($value, \FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate the length of a string
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateLength($field, $value, $params)
    {
        $length = $this->stringLength($value);
        // Length between
        if (isset($params[1])) {
            return $length >= $params[0] && $length <= $params[1];
        }
        // Length same
        return ($length !== false) && $length == $params[0];
    }

    /**
     * Validate the length of a string (between)
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateLengthBetween($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length >= $params[0] && $length <= $params[1];
    }

    /**
     * Validate the length of a string (min)
     *
     * @param string $field
     * @param mixed $value
     * @param array $params
     *
     * @return bool
     */
    protected function validateLengthMin($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length >= $params[0];
    }

    /**
     * Validate the length of a string (max)
     *
     * @param string $field
     * @param mixed $value
     * @param array $params
     *
     * @return bool
     */
    protected function validateLengthMax($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length <= $params[0];
    }

    /**
     * Get the length of a string
     *
     * @param  string $value
     * @return int|false
     */
    protected function stringLength($value)
    {
        if (!is_string($value)) {
            return false;
        } elseif (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }

    /**
     * Validate the size of a field is greater than a minimum value.
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateMin($field, $value, $params)
    {
        if (!is_numeric($value)) {
            return false;
        } elseif (function_exists('bccomp')) {
            return !(bccomp($params[0], $value, 14) === 1);
        } else {
            return $params[0] <= $value;
        }
    }

    /**
     * Validate the size of a field is less than a maximum value
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateMax($field, $value, $params)
    {
        if (!is_numeric($value)) {
            return false;
        } elseif (function_exists('bccomp')) {
            return !(bccomp($value, $params[0], 14) === 1);
        } else {
            return $params[0] >= $value;
        }
    }

    /**
     * Validate the size of a field is between min and max values
     *
     * @param  string $field
     * @param  mixed $value
     * @param  array $params
     * @return bool
     */
    protected function validateBetween($field, $value, $params)
    {
        if (!is_numeric($value)) {
            return false;
        }
        if (!isset($params[0]) || !is_array($params[0]) || count($params[0]) !== 2) {
            return false;
        }

        list($min, $max) = $params[0];

        return $this->validateMin($field, $value, array($min)) && $this->validateMax($field, $value, array($max));
    }

    /**
     * Validate a field is contained within a list of values
     * проверяет содержится ли поле $value в массиве $params
     * проверка пройдена если значение есть
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateIn($field, $value, $params)
    {
        $forceAsAssociative = false;
        if (isset($params[2])) {
            $forceAsAssociative = (bool) $params[2];
        }

        if ($forceAsAssociative || $this->isAssociativeArray($params[0])) {
            $params[0] = array_keys($params[0]);
        }

        $strict = false;
        if (isset($params[1])) {
            $strict = $params[1];
        }

        return in_array($value, $params[0], $strict);
    }

    /**
     * Validate a field is not contained within a list of values
     *  проверяет НЕ содержится ли поле $value в массиве $params
     * проверка пройдена если знаения нет
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateNotIn($field, $value, $params)
    {
        return !$this->validateIn($field, $value, $params);
    }

    /**
     * Validate a field contains a given string
     *
     * @param  string $field
     * @param  string $value
     * @param  array  $params
     * @return bool
     */
    protected function validateContains($field, $value, $params)
    {
        if (!isset($params[0])) {
            return false;
        }
        if (!is_string($params[0]) || !is_string($value)) {
            return false;
        }

        $strict = true;
        if (isset($params[1])) {
            $strict = (bool)$params[1];
        }

        if ($strict) {
            if (function_exists('mb_strpos')) {
                $isContains = mb_strpos($value, $params[0]) !== false;
            } else {
                $isContains = strpos($value, $params[0]) !== false;
            }
        } else {
            if (function_exists('mb_stripos')) {
                $isContains = mb_stripos($value, $params[0]) !== false;
            } else {
                $isContains = stripos($value, $params[0]) !== false;
            }
        }
        return $isContains;
    }

    /**
     * Validate that field array has only unique values
     *
     * @param  string $field
     * @param  array  $value
     * @return bool
     */
    protected function validateContainsUnique($field, $value)
    {
        if (!is_array($value)) {
            return false;
        }

        return $value === array_unique($value, SORT_REGULAR);
    }

    /**
     * Validate that field array has only unique values
     * Передем значение которое необходимо проеврить на уникальность
     * и массив с которым необходимо сверить на наличе такого элемента
     *
     * @param  string $field
     * @param  array  $value
     * @return bool
     */
    protected function validateUnique($field, $value, $params)
    {
        if (in_array($value, $params[0], true) === false) {
            return true;
        }
    }

    /**
     * Validate that field array has only unique values
     * Передем значение которое необходимо проверить на уникальность
     * и массив с которым необходимо сверить на наличе такого элемента
     * вернет тру если элемент найден
     *
     * @param  string $field
     * @param  array  $value
     * @return bool
     */
    protected function validateExists($field, $value, $params)
    {
        if (in_array($value, $params[0], true) === true) {
            return true;
        }
    }


    /**
     * Validate that a field is a valid e-mail address
     *
     * @param  string $field
     * @param  mixed $value
     * @return bool
     */
    protected function validateEmail($field, $value)
    {
        return filter_var($value, \FILTER_VALIDATE_EMAIL) !== false;
    }


    /**
     * Validate that a field is a valid date
     *
     * @param  string $field
     * @param  mixed $value
     * @return bool
     */
    protected function validateDate($field, $value)
    {
        $isDate = false;
        if ($value instanceof \DateTime) {
            $isDate = true;
        } else {
            $isDate = strtotime($value) !== false;
        }

        return $isDate;
    }

    /**
     * Validate that a field matches a date format
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateDateFormat($field, $value, $params)
    {
        $parsed = date_parse_from_format($params[0], $value);

        return $parsed['error_count'] === 0 && $parsed['warning_count'] === 0;
    }

    /**
     * Validate the date is before a given date
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateDateBefore($field, $value, $params)
    {
        $vtime = ($value instanceof \DateTime) ? $value->getTimestamp() : strtotime($value);
        $ptime = ($params[0] instanceof \DateTime) ? $params[0]->getTimestamp() : strtotime($params[0]);

        return $vtime < $ptime;
    }

    /**
     * Validate the date is after a given date
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateDateAfter($field, $value, $params)
    {
        $vtime = ($value instanceof \DateTime) ? $value->getTimestamp() : strtotime($value);
        $ptime = ($params[0] instanceof \DateTime) ? $params[0]->getTimestamp() : strtotime($params[0]);

        return $vtime > $ptime;
    }

    /**
     * Validate that a field contains a boolean.
     *
     * @param  string $field
     * @param  mixed $value
     * @return bool
     */
    protected function validateBoolean($field, $value)
    {
        return is_bool($value);
    }

    /**
     * Проверяет есть ли ключ в масссиве, можно отдавать на проверку массив ключей
     *
     * @param  string $field
     * @param  mixed $value
     * @return bool
     */
    protected function validateArrayHasKeys($field, $value, $params)
    {
        if (!is_array($value) || !isset($params[0])) {
            return false;
        }
        $requiredFields = $params[0];
        if (count($requiredFields) === 0) {
            return false;
        }
        foreach ($requiredFields as $fieldName) {
            if (!array_key_exists($fieldName, $value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Возвращает данные из тела запроса, которые получили из реквеста
     *
     * @return array
     */
    public function data()
    {
        return $this->_fields;
    }


    protected function getPart($data, $identifiers, $allow_empty = false)
    {
        // Catches the case where the field is an array of discrete values
        if (is_array($identifiers) && count($identifiers) === 0) {
            return array($data, false);
        }
        // Catches the case where the data isn't an array or object
        if (is_scalar($data)) {
            return array(null, false);
        }
        $identifier = array_shift($identifiers);
        // Glob match
        if ($identifier === '*') {
            $values = array();
            foreach ($data as $row) {
                list($value, $multiple) = $this->getPart($row, $identifiers, $allow_empty);
                if ($multiple) {
                    $values = array_merge($values, $value);
                } else {
                    $values[] = $value;
                }
            }
            return array($values, true);
        } // Dead end, abort
        elseif ($identifier === null || ! isset($data[$identifier])) {
            if ($allow_empty){
                //when empty values are allowed, we only care if the key exists
                return array(null, array_key_exists($identifier, $data));
            }
            return array(null, false);
        } // Match array element
        elseif (count($identifiers) === 0) {
            if ($allow_empty) {
                //when empty values are allowed, we only care if the key exists
                return array(null, array_key_exists($identifier, $data));
            }
            return array($data[$identifier], $allow_empty);
        } // We need to go deeper
        else {
            return $this->getPart($data[$identifier], $identifiers, $allow_empty);
        }
    }

    private function validationMustBeExcecuted($validation, $field, $values, $multiple){
        //always excecute requiredWith(out) rules
        if (in_array($validation['rule'], array('requiredWith', 'requiredWithout'))){
            return true;
        }

        //do not execute if the field is optional and not set
        if($this->hasRule('optional', $field) && ! isset($values)){
            return false;
        }

        //ignore empty input, except for required and accepted rule
        if (! $this->hasRule('required', $field) && ! in_array($validation['rule'], array('required', 'accepted'))){
            if($multiple){
                return count($values) != 0;
            }
            return (isset($values) && $values !== '');
        }

        return true;
    }
    /**
     * Выполняет валидацию данных находящихся в реквест, на основании правил которые переданы входным параметром,
     * для описания правил валидации используется отдельный класс ValidationRules,
     * если валидация не пройдена - на перовом правиле выбрасывается исключение
     * @return bool
     */
    public function validate(array $rules)
    {
        $this->rules($rules);

        $set_to_break = false;
        foreach ($this->_validations as $v) {
            foreach ($v['fields'] as $field) {
                list($values, $multiple) = $this->getPart($this->_fields, explode('.', $field), false);

                if (!$this->validationMustBeExcecuted($v, $field, $values, $multiple)) {
                    continue;
                }

                // Callback is user-specified or assumed method on class
                $errors = $this->getRules();
                if (isset($errors[$v['rule']])) {
                    $callback = $errors[$v['rule']];
                } else {
                    $callback = array($this, 'validate' . ucfirst($v['rule']));
                }

                if (!$multiple) {
                    $values = array($values);
                } else if (!$this->hasRule('required', $field)) {
                    $values = array_filter($values);
                }

                $result = true;
                foreach ($values as $value) {
                    $result = $result && call_user_func($callback, $field, $value, $v['params'], $this->_fields);
                }

                if (!$result) {
                    $this->error($field, $v['message'], $v['params']);

                    if ($this->stop_on_first_fail) {
                        $set_to_break = true;
                        break;
                    }
                }
            }
            if ($set_to_break) {
                break;
            }
        }
        if (count($this->errors()) !== 0) {
            $allErrors = '';
            foreach ($this->errors() as $err) {

                $allErrors .= $err[0] . ', ';
            }
            throw new HttpException('Валидация не пройдена: ' . $allErrors);
        }

        return true;
    }

    /**
     * Should the validation stop a rule is failed
     * @param bool $stop
     */
    public function stopOnFirstFail($stop = true)
    {
        $this->stop_on_first_fail = (bool)$stop;
    }

    /**
     * Returns all rule callbacks, the static and instance ones.
     * Возвращает массив правил валидации, все что есть
     * @return array
     */
    protected function getRules()
    {
        return array_merge($this->_instanceRules, static::$_rules);
    }

    /**
     * Returns all rule message, the static and instance ones.
     *
     * @return array
     */
    protected function getRuleMessages()
    {
        return array_merge($this->_instanceRuleMessage, static::$_ruleMessages);
    }

    /**
     * Determine whether a field is being validated by the given rule.
     *
     * @param  string  $name  The name of the rule
     * @param  string  $field The name of the field
     * @return bool
     */
    protected function hasRule($name, $field)
    {
        foreach ($this->_validations as $validation) {
            if ($validation['rule'] == $name && in_array($field, $validation['fields'])) {
                return true;
            }
        }

        return false;
    }

    protected static function assertRuleCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(
                'Second argument must be a valid callback. Given argument was not callable.'
            );
        }
    }

    /**
     * Adds a new validation rule callback that is tied to the current
     * instance only.
     *
     * @param string $name
     * @param callable $callback
     * @param string $message
     * @throws \InvalidArgumentException
     */
    public function addInstanceRule($name, $callback, $message = null)
    {
        static::assertRuleCallback($callback);

        $this->_instanceRules[$name] = $callback;
        $this->_instanceRuleMessage[$name] = $message;
    }

    /**
     * Register new validation rule callback
     * можно написать свое правило через ананимную функцию
     *
     * @param string $name
     * @param callable $callback
     * @param string $message
     * @throws \InvalidArgumentException
     */
    public static function addRule($name, $callback, $message = null)
    {
        if ($message === null) {
            $message = static::ERROR_DEFAULT;
        }

        static::assertRuleCallback($callback);

        static::$_rules[$name] = $callback;
        static::$_ruleMessages[$name] = $message;
    }

    /**
     * @param  mixed $fields
     * @return string
     */
    public function getUniqueRuleName($fields)
    {
        if (is_array($fields)) {
            $fields = implode("_", $fields);
        }

        $orgName = "{$fields}_rule";
        $name = $orgName;
        $rules = $this->getRules();
        while (isset($rules[$name])) {
            $name = $orgName . "_" . rand(0, 10000);
        }

        return $name;
    }

    /**
     * Returns true if either a validator with the given name has been
     * registered or there is a default validator by that name.
     *
     * @param string $name
     * @return bool
     */
    public function hasValidator($name)
    {
        $rules = $this->getRules();
        return method_exists($this, "validate" . ucfirst($name))
            || isset($rules[$name]);
    }

    /**
     * Convenience method to add a single validation rule
     * На основании полученного массива опредляет какие проверки нужно сделать, можно описывать только одно правило
     * и проверяет наличие метода который выполнит проверку
     *
     * @param string|callable $rule
     * @param array|string $fields
     * @return Validator
     * @throws \InvalidArgumentException
     */
    public function rule($rule, $fields)
    {
        // Get any other arguments passed to function
        $params = array_slice(func_get_args(), 2);

        if (is_callable($rule)
            && !(is_string($rule) && $this->hasValidator($rule))) {
            $name = $this->getUniqueRuleName($fields);
            $message = isset($params[0]) ? $params[0] : null;
            $this->addInstanceRule($name, $rule, $message);
            $rule = $name;
        }

        $errors = $this->getRules();
        if (!isset($errors[$rule])) {
            $ruleMethod = 'validate' . ucfirst($rule);
            if (!method_exists($this, $ruleMethod)) {
                throw new \InvalidArgumentException(
                    "Rule '" . $rule . "' has not been registered with " . get_called_class() . "::addRule()."
                );
            }
        }

        // Ensure rule has an accompanying message
        $messages = $this->getRuleMessages();
        $message = isset($messages[$rule]) ? $messages[$rule] : self::ERROR_DEFAULT;

        // Ensure message contains field label
        if (function_exists('mb_strpos')) {
            $notContains = mb_strpos($message, '{field}') === false;
        } else {
            $notContains = strpos($message, '{field}') === false;
        }
        if ($notContains) {
            $message = '{field} ' . $message;
        }

        $this->_validations[] = array(
            'rule' => $rule,
            'fields' => (array)$fields,
            'params' => (array)$params,
            'message' => $message
        );

        return $this;
    }

    /**
     * Add label to rule
     *
     * @param  string $value
     * @return Validator
     */
    public function label($value)
    {
        $lastRules = $this->_validations[count($this->_validations) - 1]['fields'];
        $this->labels(array($lastRules[0] => $value));

        return $this;
    }

    /**
     * Add labels to rules
     *
     * @param  array  $labels
     * @return Validator
     */
    public function labels($labels = array())
    {
        $this->_labels = array_merge($this->_labels, $labels);

        return $this;
    }

    /**
     * @param  string $field
     * @param  string $message
     * @param  array  $params
     * @return array
     */
    protected function checkAndSetLabel($field, $message, $params)
    {
        if (isset($this->_labels[$field])) {
            $message = str_replace('{field}', $this->_labels[$field], $message);

            if (is_array($params)) {
                $i = 1;
                foreach ($params as $k => $v) {
                    $tag = '{field' . $i . '}';
                    $label = isset($params[$k]) && (is_numeric($params[$k]) || is_string($params[$k])) && isset($this->_labels[$params[$k]]) ? $this->_labels[$params[$k]] : $tag;
                    $message = str_replace($tag, $label, $message);
                    $i++;
                }
            }
        } else {
            $message = $this->prepend_labels
                ? str_replace('{field}', ucwords(str_replace('_', ' ', $field)), $message)
                : str_replace('{field} ', '', $message);
        }

        return $message;
    }

    /**
     * Convenience method to add multiple validation rules with an array
     * обрабатывает правила, можно описывать сразу все необходимые правила по множеству реквизитов
     * используем только его как предпочтительный
     *
     * @param array $rules
     */
    public function rules($rules)
    {
        foreach ($rules as $ruleType => $params) {
            if (is_array($params)) {
                foreach ($params as $innerParams) {
                    if (!is_array($innerParams)) {
                        $innerParams = (array)$innerParams;
                    }
                    array_unshift($innerParams, $ruleType);
                    call_user_func_array(array($this, 'rule'), $innerParams);
                }
            } else {
                $this->rule($ruleType, $params);
            }
        }
    }

    /**
     * Replace data on cloned instance
     *
     * @param  array $data
     * @param  array $fields
     * @return Validator
     */
    public function withData($data, $fields = array())
    {
        $clone = clone $this;
        $clone->_fields = !empty($fields) ? array_intersect_key($data, array_flip($fields)) : $data;
        $clone->_errors = array();
        return $clone;
    }

    /**
     * Convenience method to add validation rule(s) by field
     *
     * @param string $field
     * @param array  $rules
     */
    public function mapFieldRules($field, $rules)
    {
        $me = $this;

        array_map(function ($rule) use ($field, $me) {

            //rule must be an array
            $rule = (array)$rule;

            //First element is the name of the rule
            $ruleName = array_shift($rule);

            //find a custom message, if any
            $message = null;
            if (isset($rule['message'])) {
                $message = $rule['message'];
                unset($rule['message']);
            }
            //Add the field and additional parameters to the rule
            $added = call_user_func_array(array($me, 'rule'), array_merge(array($ruleName, $field), $rule));
            if (!empty($message)) {
                $added->message($message);
            }
        }, (array)$rules);
    }

    /**
     * Convenience method to add validation rule(s) for multiple fields
     *
     * @param array $rules
     */
    public function mapFieldsRules($rules)
    {
        $me = $this;
        array_map(function ($field) use ($rules, $me) {
            $me->mapFieldRules($field, $rules[$field]);
        }, array_keys($rules));
    }
    /**
     * Get array of error messages
     * содержит все ошибки валидации, можно использовать под флэш уведомления,
     * но в текущей реализации выбрасываем исключение при появлении первой ошибки
     *
     * @param  null|string $field
     * @return array|bool
     */
    public function errors($field = null)
    {
        if ($field !== null) {
            return isset($this->_errors[$field]) ? $this->_errors[$field] : false;
        }

        return $this->_errors;
    }
/**
* @param string $field
* @param string $message
* @param array  $params
*/
    public function error($field, $message, array $params = array())
    {
        $message = $this->checkAndSetLabel($field, $message, $params);

        $values = array();
        // Printed values need to be in string format
        foreach ($params as $param) {
            if (is_array($param)) {
                $param = "['" . implode("', '", $param) . "']";
            }
            if ($param instanceof \DateTime) {
                $param = $param->format('Y-m-d');
            } else {
                if (is_object($param)) {
                    $param = get_class($param);
                }
            }
            // Use custom label instead of field name if set
            if (is_string($params[0]) && isset($this->_labels[$param])) {
                $param = $this->_labels[$param];
            }
            $values[] = $param;
        }

        $this->_errors[$field][] = vsprintf($message, $values);
    }
    /**
     * проверяет ассоциативный массив или нет, через поиск ключа отличного от числа, хотя бы один
     * @param array  $input
     */
    private function isAssociativeArray($input){
        //array contains at least one key that's not an can not be cast to an integer
        return count(array_filter(array_keys($input), 'is_string')) > 0;
    }

}