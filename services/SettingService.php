<?php
/**
 * JMJ Enterprises Solutions - Global Setting Service
 */

declare(strict_types=1);

namespace Services;

use Core\Database;

class SettingService {
    private static ?array $cachedSettings = null;

    public static function getAll(): array {
        if (self::$cachedSettings !== null) {
            return self::$cachedSettings;
        }

        try {
            $db = Database::getInstance();
            $rows = $db->fetchAll("SELECT key_name, key_value FROM settings");
            $settings = [];
            foreach ($rows as $r) {
                $settings[$r['key_name']] = $r['key_value'];
            }
            self::$cachedSettings = $settings;
            return $settings;
        } catch (\Throwable) {
            return [];
        }
    }

    public static function get(string $key, mixed $default = null): mixed {
        $all = self::getAll();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): void {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO `settings` (`setting_group`, `key_name`, `key_value`, `field_type`) 
             VALUES (:group, :key, :val, :type) 
             ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`), `setting_group` = VALUES(`setting_group`)",
            [
                'group' => $group,
                'key' => $key,
                'val' => (string)$value,
                'type' => $type
            ]
        );
        self::$cachedSettings = null;
    }
}
