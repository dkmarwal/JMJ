<?php
/**
 * JMJ Enterprises Solutions - Media Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;

class Media {
    public static function all(int $page = 1, int $limit = 24, ?string $folder = null, ?string $search = null): array {
        $db = Database::getInstance();
        $offset = ($page - 1) * $limit;
        $where = "WHERE is_archived = 0";
        $params = [];

        if (!empty($folder) && $folder !== 'all') {
            $where .= " AND folder = :folder";
            $params['folder'] = $folder;
        }

        if (!empty($search)) {
            $where .= " AND (original_filename LIKE :s OR alt_text LIKE :s)";
            $params['s'] = '%' . $search . '%';
        }

        $total = (int)$db->fetchColumn("SELECT COUNT(*) FROM media {$where}", $params);
        $items = $db->fetchAll("SELECT * FROM media {$where} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}", $params);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => (int)ceil($total / $limit)
        ];
    }
}
