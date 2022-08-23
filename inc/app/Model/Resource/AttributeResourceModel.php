<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Resource;

class AttributeResourceModel implements ResourceInterface
{
    private const TABLE_ENTITY = 'foks_attribute';

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
                  slug varchar(190) NOT NULL,
                  name varchar(190) NOT NULL,
                  PRIMARY KEY  (id)
                ) $charsetCollate;";

        $wpdb->query($sql);

        $wpdb->get_var($query) === $table;
    }

    /**
     * @param array $data
     * @return void
     */
    public static function set(array $data): void
    {
        global $wpdb;

        $table = $wpdb->base_prefix . self::TABLE_ENTITY;

        $wpdb->insert($table, [
            'slug' => $data['slug'],
            'name' => $data['name'],
        ]);
    }

    /**
     * @return array|object|null
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

    public static function getNameByIds($ids) {
        global $wpdb;

        $table = $wpdb->base_prefix . self::TABLE_ENTITY;
        $implodeIds = implode(',', $ids);

        return $wpdb->get_row("SELECT GROUP_CONCAT(name) as names FROM $table WHERE id IN ($implodeIds)");
    }
}
