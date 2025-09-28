<?php

namespace Core;

class Validator
{
    private $errors = [];

    public function validate($data, $rules)
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $this->validateField($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    private function validateField($field, $value, $rule)
    {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleValue = $ruleParts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (strlen(trim($value)) === 0) {
                    $this->errors[$field] = ucfirst($field) . ' cannot be empty';
                }
                break;

            case 'max':
                if (strlen($value) > $ruleValue) {
                    $this->errors[$field] = ucfirst($field) . " must be less than {$ruleValue} characters";
                }
                break;

            case 'min':
                if (strlen($value) < $ruleValue) {
                    $this->errors[$field] = ucfirst($field) . " must be at least {$ruleValue} characters";
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = ucfirst($field) . " must be a valid email address";
                }
                break;
        }
    }

    public function errors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getError($field)
    {
        return $this->errors[$field] ?? null;
    }
}
