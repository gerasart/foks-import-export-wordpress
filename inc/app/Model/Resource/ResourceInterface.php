<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

namespace Foks\Model\Resource;

interface ResourceInterface
{
    /**
     * @return void
     */
    public function create() : void;

    /**
     * @param array $data
     * @return void
     */
    public static function set(array $data) : void;

    /**
     * @return array|object|null
     */
    public static function getList();

    /**
     * @return void
     */
    public static function delete() : void;
}
