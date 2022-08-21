<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Resource;

class LogResourceModel implements ResourceInterface
{
    private const TABLE_ENTITY = 'foks_log';

    public function __construct()
    {
        $this->create();
    }

    /**
     * @return void
     */
    public function create(): void
    {
        global $wpdb;

        $table = $wpdb->base_prefix . self::TABLE_ENTITY;

        $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table));

        if ($wpdb->get_var($query) === $table) {
            return;
        }

        $charsetCollate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE `$table` (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  action varchar(190) NOT NULL,
                  message text NOT NULL,
                  created_at datetime NOT NULL,
                  PRIMARY KEY  (id)
                ) $charsetCollate;";

        $wpdb->query($sql);

        $wpdb->get_var($query) === $table;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public static function set(array $data): void
    {
        global $wpdb;

        $table = $wpdb->base_prefix . self::TABLE_ENTITY;

        $wpdb->insert($table, [
            'action' => $data['action'],
            'message' => $data['message'],
            'created_at' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return array|object|\stdClass[]|null
     */
    public static function getList()
    {
        global $wpdb;

        $table = $wpdb->base_prefix . self::TABLE_ENTITY;

        return $wpdb->get_results("SELECT * FROM $table");
    }

    /**
     * @return void
     */
    public static function delete(): void
    {
        global $wpdb;
        $table = $wpdb->base_prefix . self::TABLE_ENTITY;

        $wpdb->query("TRUNCATE $table");
    }
}
