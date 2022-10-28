<?php

namespace App\Constants;

class FamilyMembersType extends Enum
{
    const BROTHER = 'brother';
    const SISTER = 'sister';
    const MOTHER = 'mother';
    const FATHER = 'father';
    const WIFE = 'wife';
    const HUSBAND = 'husband';
    const OFFSPRING = 'offspring';
    const OWNER = 'owner';

    public static function familyMembersTypeList()
    {
        return [
            self::OWNER => 'صاحب سلسله',
            self::HUSBAND => 'شوهر',
            self::WIFE => 'همسر',
            self::BROTHER => 'برادر',
            self::SISTER => 'خواهر',
            self::MOTHER => 'مادر',
            self::FATHER => 'پدر',
            self::OFFSPRING => 'فرزند'
        ];
    }
}
