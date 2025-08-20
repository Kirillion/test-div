<?php

namespace app\components\interfaces;

interface BatchCreateActiveRecordInteface
{
    public static function batchCreate(array $data): array;
}