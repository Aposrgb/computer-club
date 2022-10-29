<?php

namespace App\Service;

class HelperService
{
    public function getActualValue($userField, $dtoField)
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

    public function getActualValueBool($userField, $dtoField)
    {
        if (!is_null($userField)) {
            if (!is_null($dtoField)) {
                $returnedField = $dtoField;
            } else {
                $returnedField = $userField;
            }
        } else {
            $returnedField = $dtoField;
        }
        return $returnedField;
    }

    public function getActualValueNumber($userField, $dtoField): float|int|string|null
    {
        $returnedField = null;
        if (is_numeric($userField)) {
            if (is_numeric($dtoField)) {
                $returnedField = $dtoField;
            } else {
                $returnedField = $userField;
            }
        } else {
            if (is_numeric($dtoField)) {
                $returnedField = $dtoField;
            }
        }
        return $returnedField;
    }

    public function getActualValueArray($userField, $dtoField)
    {
        $returnedField = [];
        if (!empty($dtoField)) {
            $returnedField = $dtoField;
        } else {
            if (!empty($userField)) {
                $returnedField = $userField;
            }
        }
        return $returnedField;
    }

    public function getActualValueDate(?\DateTimeInterface $userDate, ?string $dtoDate): ?\DateTimeInterface
    {
        $returnedField = null;
        if ($userDate or $dtoDate) {
            if ($userDate) {
                if ($dtoDate) {
                    $returnedField = new \DateTime($dtoDate);
                } else {
                    $returnedField = $userDate;
                }
            } else {
                $returnedField = new \DateTime($dtoDate);
            }
        }
        return $returnedField;
    }

}