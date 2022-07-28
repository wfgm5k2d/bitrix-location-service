<?php

/**
 * Используется как зависимость
 */
interface IBitrixLocation {
    public static function getLocation($sSearchParam, $nPageSize = 10, $page = 1);
}