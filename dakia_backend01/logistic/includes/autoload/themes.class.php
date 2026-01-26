<?php
class Themes extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = [
            'id' => 'number',
            'name' => 'string',
            'slug' => 'string',
            'style_sheet' => 'string',
            'dashboard_template' => 'string',
            'is_active' => 'bit',
            'created_at' => 'datetime',
            'created_by' => 'number'
        ];
        parent::__construct("themes", 'id', $fieldList, $mixedCreator);
    }

    public static function getThemesListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfThemesFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteThemeFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteThemeByThemeId($themeId)
    {
        if ($themeId != "" && $themeId > 0) {
            self::runQuery("DELETE FROM themes WHERE id = '" . DbAccess3::escape($themeId) . "'");
        }
    }

}

?>