<?php

namespace App;

class Consts
{
    const TRUE = 1;
    const FALSE = 0;

    const MASTERDATA_TABLES = [
        'platforms',
        'bounty_types',
        'reasons',
        'languages'
    ];

    const DEFAULT_PER_PAGE = 10;

    const ROLE_SUPER_ADMIN  = 'Super Admin';
    const ROLE_ADMIN        = 'Admin';

    const CHAR_COMMA = ',';
    const CHAR_COLON = ':';
    const CHAR_SPACE = ' ';
    const CHAR_UNDERSCORE = '_';
    const STRING_EMPTY = '';

    const REGEX_REMOVE_SPECIAL_CHAR = '/^[\pZ\p{Cc}\x{feff}]+|[\pZ\p{Cc}\x{feff}]+$/ux';

    const TEMPLATE_USER = [
        'id',
        'email',
        'password',
        'msv',
        'username',
        'full_name',
        'dob',
        'sex',
        'course',
        'unit'
    ];

    const TEMPLATE_USER_CLASS = [
        'id',
        'msv',
        'full_name',
        'is_valid',
    ];
}
