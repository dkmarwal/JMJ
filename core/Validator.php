<?php
/**
 * JMJ Enterprises Solutions - Input Validation & Sanitization Engine
 */

declare(strict_types=1);

namespace Core;

class Validator {
    private array $data;
    private array $errors = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self {
        $validator = new self($data);
        $validator->validate($rules);
        return $validator;
    }

    public function validate(array $rules): void {
        foreach ($rules as $field => $fieldRules) {
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $this->data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $rule = trim($rule);

                if ($rule === 'required' && (empty($value) && $value !== '0')) {
                    $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . ' is required.');
                    break;
                }

                if (!empty($value)) {
                    if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->addError($field, 'Please enter a valid email address.');
                    }
                    if ($rule === 'min' && strlen((string)$value) < (int)$params[0]) {
                        $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be at least {$params[0]} characters.");
                    }
                    if ($rule === 'max' && strlen((string)$value) > (int)$params[0]) {
                        $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$params[0]} characters.");
                    }
                    if ($rule === 'phone' && !preg_match('/^[0-9+\-\s()]{7,20}$/', (string)$value)) {
                        $this->addError($field, 'Please enter a valid phone number.');
                    }
                    if ($rule === 'numeric' && !is_numeric($value)) {
                        $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . ' must be a number.');
                    }
                }
            }
        }
    }

    private function addError(string $field, string $message): void {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): ?string {
        return reset($this->errors) ?: null;
    }
}
