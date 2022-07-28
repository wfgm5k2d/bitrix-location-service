<?php

/**
 * Получение региона местоположений
 * Возвращает метод get для каждого поля из массива Битрикс
 * Перед использованием импортируйте все местоположения в Битрикс
 */
class BitrixLocationRegionService implements IBitrixLocationRegion
{
    /**
     * @var array
     */
    public array $arRegion = [];

    /**
     * @param $arBitrixRegion
     */
    public function __construct($arBitrixRegion)
    {
        $this->arRegion = $arBitrixRegion;
    }

    /**
     * @param $arBitrixRegion
     * @return BitrixLocationRegionService
     */
    public static function getLocationRegion($arBitrixRegion) {
        return new BitrixLocationRegionService($arBitrixRegion);
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->arRegion['ID'];
    }

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->arRegion['CODE'];
    }

    /**
     * @return mixed
     */
    public function getLeftMargin() {
        return $this->arRegion['LEFT_MARGIN'];
    }

    /**
     * @return mixed
     */
    public function getRightMargin() {
        return $this->arRegion['RIGHT_MARGIN'];
    }

    /**
     * @return mixed
     */
    public function getDepthLevel() {
        return $this->arRegion['DEPTH_LEVEL'];
    }

    /**
     * @return mixed
     */
    public function getSort() {
        return $this->arRegion['SORT'];
    }

    /**
     * @return mixed
     */
    public function getParentId() {
        return $this->arRegion['PARENT_ID'];
    }

    /**
     * @return mixed
     */
    public function getTypeId() {
        return $this->arRegion['TYPE_ID'];
    }

    /**
     * @return mixed
     */
    public function getLatitude() {
        return $this->arRegion['LATITUDE'];
    }

    /**
     * @return mixed
     */
    public function getLongitude() {
        return $this->arRegion['LONGITUDE'];
    }

    /**
     * @return mixed
     */
    public function getCountryId() {
        return $this->arRegion['COUNTRY_ID'];
    }

    /**
     * @return mixed
     */
    public function getRegionId() {
        return $this->arRegion['REGION_ID'];
    }

    /**
     * @return mixed
     */
    public function getCityId() {
        return $this->arRegion['CITY_ID'];
    }

    /**
     * @return mixed
     */
    public function getLocDefault() {
        return $this->arRegion['LOC_DEFAULT'];
    }

    /**
     * @return mixed
     */
    public function getNameRu() {
        return $this->arRegion['NAME_RU'];
    }

    /**
     * @return mixed
     */
    public function getTypeCode() {
        return $this->arRegion['TYPE_CODE'];
    }
}