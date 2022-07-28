<?php

/**
 * Класс для работы с местоположениями Битрикс
 * Возвращает метод get для каждого поля из массива Битрикс
 * Перед использованием импортируйте все местоположения в Битрикс
 */
class BitrixLocationService implements IBitrixLocation
{
    /**
     * @var array
     */
    private array $arBitrixRegion = [];

    /**
     * @var array
     */
    private array $arLocations = [];

    /**
     * Страна
     * Country
     */
    public const TYPE_COUNTRY = "1";

    /**
     * Округ
     * District
     */
    public const TYPE_COUNTRY_DISTRICT = "2";

    /**
     * Область
     * Region
     */
    public const TYPE_REGION = "3";

    /**
     * Район области
     * Area
     */
    public const TYPE_SUBREGION = "4";

    /**
     * Город
     * City
     */
    public const TYPE_CITY = "5";

    /**
     * Село
     * Town
     */
    public const TYPE_VILLAGE = "6";

    /**
     * @param $sSearchParam
     * @param $nPageSize
     * @param $page
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($sSearchParam, $nPageSize = 10, $page = 1)
    {
        $res = \Bitrix\Sale\Location\LocationTable::getList(array(
            'filter' => array("TYPE_ID" => ["5", "6"], "%NAME_RU" => $sSearchParam),
            'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE'),
            'limit' => $nPageSize,
            'offset' => ($page - 1) * $nPageSize,
        ));
        while ($item = $res->fetch()) {
            $ID = \CSaleLocation::getLocationIDbyCODE($item['CODE']);
            $arVal = \CSaleLocation::GetByID($ID, "ru"); // параметр ru необязательный. По умолчанию текущий язык.

            $this->arLocationsBitrix['ITEM'] = $item;
            $this->arLocationsBitrix['VALUE'] = $arVal;
            $this->arLocationsBitrix['ID'] = $ID;
            $this->arLocationsBitrix['FORMAT'][] = [
                'CODE' => $item['CODE'],
                'REGION_NAME' => $arVal['REGION_NAME'],
                'REGION_ID' => $arVal['REGION_ID'],
                'CITY_NAME' => $arVal['CITY_NAME'],
                'CITY_ID' => $arVal['CITY_ID'],
            ];
        }
    }

    /**
     * @param $sSearchParam
     * @param $nPageSize
     * @param $page
     * @return BitrixLocationService
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getLocation($sSearchParam, $nPageSize = 10, $page = 1)
    {
        return new BitrixLocationService($sSearchParam, $nPageSize, $page);
    }

    /**
     * @param $nRegionId
     * @return BitrixLocationRegionService
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * Зависит от класса BitrixLocationRegionService
     * Поиск по Области, Округу и Району
     */
    public function getBitrixRegion($nRegionId)
    {
        $res = \Bitrix\Sale\Location\LocationTable::getList(array(
            'filter' => array('REGION_ID' => $nRegionId, "TYPE_ID" => ["2", "3", "4"]),
            'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE')
        ));

        $this->arBitrixRegion = $res->fetch();

        return BitrixLocationRegionService::getLocationRegion($this->arBitrixRegion);
    }

    /**
     * @param $nTypeId
     * @param $sNameLocation
     * @param $sLanguageId
     * @param $arSelect
     * @return array|string[]
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * Поиск локации по определенному типу. Допускается поиск по массиву типов в $nTypeId
     */
    public function searchLocationInBitrixLocations($nTypeId, $sNameLocation, $sLanguageId = LANGUAGE_ID, $arSelect = ['ID', 'CODE', 'TYPE_ID', 'CITY_ID', 'NAME_RU' => 'NAME.NAME', 'PARENT_ID', 'REGION_ID'])
    {
        $result = \Bitrix\Sale\Location\LocationTable::getList(array(
            'filter' => array('TYPE_ID' => $nTypeId, '%NAME_RU' => $sNameLocation, '=NAME.LANGUAGE_ID' => $sLanguageId),
            'select' => $arSelect
        ));
        while ($obCity = $result->fetch()) {
            $this->arLocations = $obCity;
        }

        if (empty($this->arLocations)) {
            $this->arLocations = ['Доставка в выбранное местоположение невозможна! Пожалуйста выберите другое место доставки. (Выберите ближайший город. При оформлении заказа вы сможете уточнить место доставки)'];
        }

        return $this->arLocations;
    }

    /**
     * @param $nId
     * @return mixed|string
     * Получить индекс местоположения по ID местоположения
     */
    public static function getZipLocation($nId = '')
    {
        $nZip = '';

        $arZip = \CSaleLocation::GetLocationZIP($nId);
        foreach ($arZip as $index) {
            $nZip = $index['ZIP'];
        }

        return $nZip;
    }

    /**
     * @param $arLocation
     * @return array
     * Получить полный путь местоположения
     */
    public static function getFullPathToLocation($arLocation)
    {
        $arCities = [];

        $db_vars = \CSaleLocation::GetList(
            array(
                "SORT" => "ASC",
                "COUNTRY_NAME_LANG" => "ASC",
                "CITY_NAME_LANG" => "ASC"
            ),
            array("LID" => LANGUAGE_ID, 'CITY_ID' => $arLocation['CITY_ID']),
            false,
            false,
            array('*')
        );
        while ($vars = $db_vars->Fetch()){
            $arCities = $vars;
            $arCities['FULL_PATH'] = [
                $vars['COUNTRY_NAME'],
                $vars['REGION_NAME'],
                $vars['CITY_NAME'],
            ];
        }

        return $arCities;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->arLocationsBitrix['ITEM']['ID'];
    }

    /**
     * @return mixed
     */
    public function getItemCode()
    {
        return $this->arLocationsBitrix['ITEM']['CODE'];
    }

    /**
     * @return mixed
     */
    public function getItemLeftMargin()
    {
        return $this->arLocationsBitrix['ITEM']['LEFT_MARGIN'];
    }

    /**
     * @return mixed
     */
    public function getItemRightMargin()
    {
        return $this->arLocationsBitrix['ITEM']['RIGHT_MARGIN'];
    }

    /**
     * @return mixed
     */
    public function getItemDepthLevel()
    {
        return $this->arLocationsBitrix['ITEM']['DEPTH_LEVEL'];
    }

    /**
     * @return mixed
     */
    public function getItemSort()
    {
        return $this->arLocationsBitrix['ITEM']['SORT'];
    }

    /**
     * @return mixed
     */
    public function getItemParentId()
    {
        return $this->arLocationsBitrix['ITEM']['PARENT_ID'];
    }

    /**
     * @return mixed
     */
    public function getItemTypeId()
    {
        return $this->arLocationsBitrix['ITEM']['TYPE_ID'];
    }

    /**
     * @return mixed
     */
    public function getItemLatitude()
    {
        return $this->arLocationsBitrix['ITEM']['LATITUDE'];
    }

    /**
     * @return mixed
     */
    public function getItemLongitude()
    {
        return $this->arLocationsBitrix['ITEM']['LONGITUDE'];
    }

    /**
     * @return mixed
     */
    public function getItemCountryId()
    {
        return $this->arLocationsBitrix['ITEM']['COUNTRY_ID'];
    }

    /**
     * @return mixed
     */
    public function getItemRegionId()
    {
        return $this->arLocationsBitrix['ITEM']['REGION_ID'];
    }

    /**
     * @return mixed
     */
    public function getItemCityId()
    {
        return $this->arLocationsBitrix['ITEM']['CITY_ID'];
    }

    /**
     * @return mixed
     */
    public function getItemLocDefault()
    {
        return $this->arLocationsBitrix['ITEM']['LOC_DEFAULT'];
    }

    /**
     * @return mixed
     */
    public function getItemNameRu()
    {
        return $this->arLocationsBitrix['ITEM']['NAME_RU'];
    }

    /**
     * @return mixed
     */
    public function getItemTypeCode()
    {
        return $this->arLocationsBitrix['ITEM']['TYPE_CODE'];
    }

    /**
     * @return mixed
     */
    public function getValueId()
    {
        return $this->arLocationsBitrix['VALUE']['ID'];
    }

    /**
     * @return mixed
     */
    public function getValueSort()
    {
        return $this->arLocationsBitrix['VALUE']['SORT'];
    }

    /**
     * @return mixed
     */
    public function getValueCode()
    {
        return $this->arLocationsBitrix['VALUE']['CODE'];
    }

    /**
     * @return mixed
     */
    public function getValueCountryName()
    {
        return $this->arLocationsBitrix['VALUE']['COUNTRY_NAME'];
    }

    /**
     * @return mixed
     */
    public function getValueCountryShortName()
    {
        return $this->arLocationsBitrix['VALUE']['COUNTRY_SHORT_NAME'];
    }

    /**
     * @return mixed
     */
    public function getValueCountryId()
    {
        return $this->arLocationsBitrix['VALUE']['COUNTRY_ID'];
    }

    /**
     * @return mixed
     */
    public function getValueCountryNameOrig()
    {
        return $this->arLocationsBitrix['VALUE']['COUNTRY_NAME_ORIG'];
    }

    /**
     * @return mixed
     */
    public function getValueCountryNameLang()
    {
        return $this->arLocationsBitrix['VALUE']['COUNTRY_NAME_LANG'];
    }

    /**
     * @return mixed
     */
    public function getValueRegionName()
    {
        return $this->arLocationsBitrix['VALUE']['REGION_NAME'];
    }

    /**
     * @return mixed
     */
    public function getValueRegionShortName()
    {
        return $this->arLocationsBitrix['VALUE']['REGION_SHORT_NAME'];
    }

    /**
     * @return mixed
     */
    public function getValueRegionId()
    {
        return $this->arLocationsBitrix['VALUE']['REGION_ID'];
    }

    /**
     * @return mixed
     */
    public function getValueRegionNameOrig()
    {
        return $this->arLocationsBitrix['VALUE']['REGION_NAME_ORIG'];
    }

    /**
     * @return mixed
     */
    public function getValueRegionNameLang()
    {
        return $this->arLocationsBitrix['VALUE']['REGION_NAME_LANG'];
    }

    /**
     * @return mixed
     */
    public function getValueCityName()
    {
        return $this->arLocationsBitrix['VALUE']['CITY_NAME'];
    }

    /**
     * @return mixed
     */
    public function getValueCityShortName()
    {
        return $this->arLocationsBitrix['VALUE']['CITY_SHORT_NAME'];
    }

    /**
     * @return mixed
     */
    public function getValueCityId()
    {
        return $this->arLocationsBitrix['VALUE']['CITY_ID'];
    }

    /**
     * @return mixed
     */
    public function getValueCityNameOrig()
    {
        return $this->arLocationsBitrix['VALUE']['CITY_NAME_ORIG'];
    }

    /**
     * @return mixed
     */
    public function getValueCityNameLang()
    {
        return $this->arLocationsBitrix['VALUE']['CITY_NAME_LANG'];
    }

    /**
     * @return mixed
     */
    public function getLocationId()
    {
        return $this->arLocationsBitrix['ID'];
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->arLocationsBitrix['FORMAT'];
    }

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->arBitrixRegion['ID'];
    }
}