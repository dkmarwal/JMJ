<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Input Validation & Sanitation Engine
 */

declare(strict_types=1);

namespace Core;

class Validator {
    private array $errors = [];
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self {
        $validator = new self($data);
        $validator->validate($rules);
        return $validator;
    }

    public function validate(array $rules): void {
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $this->$method($field, $value, $params);
                }
            }
        }
    }

    private function validateRequired(string $field, mixed $value, array $params): void {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " is required.");
        }
    }

    private function validateEmail(string $field, mixed $value, array $params): void {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "Please enter a valid email address.");
        }
    }

    private function validateNumeric(string $field, mixed $value, array $params): void {
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be a numeric value.");
        }
    }

    private function validateMin(string $field, mixed $value, array $params): void {
        $min = (int)($params[0] ?? 0);
        if (is_string($value) && mb_strlen($value) < $min) {
            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.");
        } elseif (is_numeric($value) && (float)$value < $min) {
            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min}.");
        }
    }

    private function validateMax(string $field, mixed $value, array $params): void {
        $max = (int)($params[0] ?? 0);
        if (is_string($value) && mb_strlen($value) > $max) {
            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$max} characters.");
        } elseif (is_numeric($value) && (float)$value > $max) {
            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$max}.");
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
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }

    private function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }
}
