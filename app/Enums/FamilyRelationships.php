<?php

namespace App\Enums;

enum FamilyRelationships:string {
    case BROTHER = 'brother';
    case SISTER = 'sister';
    case FATHER = 'father';
    case MOTHER = 'mother';
    case HUSBAND = 'husband';
    case WIFE = 'wife';
    case OFFSPRING = 'offspring';
}
