<?php

namespace App\Helper\Trait;

trait HelperTrait
{
    private function getActualValue($userField, $dtoField)
    {
        $returnedField = null;
        if ($userField or $dtoField) {
            if ($userField) {
                if ($dtoField) {
                    $returnedField = $dtoField;
                } else {
                    $returnedField = $userField;
                }
            } else {
                $returnedField = $dtoField;
            }
        }
        return $returnedField;
    }

}