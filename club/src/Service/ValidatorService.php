<?php

namespace App\Service;

use App\Helper\EnumRoles\UserRoles;
use App\Helper\Exception\ApiException;
use App\Helper\Mapped\Helper;
use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validate($body = [], $groupsBody = []): void
    {
        $validationError = [];
        $groupsBody[] = 'pagination';
        $bodyError = $this->validator->validate($body, groups: $groupsBody);
        $invalid_field = [];
        /** @var ConstraintViolation $error */
        foreach ($bodyError as $error) {
            $invalid_field[] = [
                'name' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }
        $validationError['body'] = $invalid_field;

        if (count($bodyError) > 0)
            throw new ApiException(message: 'Ошибки при выполнении запроса', validationError: $validationError, status: Response::HTTP_BAD_REQUEST);
    }

    /** @param UploadedFile[] $files */
    public function validateImagesExtension(array $files, array $extensions): void
    {
        foreach ($files as $file) {
            if ($file) {
                if (!in_array($file->getMimeType(), $extensions)) {
                    throw new ApiException(
                        message: 'Не поддерживаемый формат изображения (поддерживается '
                        . join(',', $extensions) .
                        ')',
                    );
                }
            }
        }
    }


    public static function validateDate($object, ExecutionContextInterface $context): void
    {
        if ($object) {
            try {
                new DateTime($object);
            } catch (\Exception $e) {
                $context->buildViolation('Значение ' . $object . ' не является допустимой датой (верный формат: ГГГГ-ММ-ДД)')->addViolation();
            }
        }
    }

    public static function validateInteger($object, ExecutionContextInterface $context): void
    {
        if (!is_numeric($object) && $object != "")
            $context->buildViolation('Значение `' . $object . '` не является допустимым int')->addViolation();
    }

    public function validateMaxSizeInteger($object): void
    {
        if (is_array($object)) {
            foreach ($object as $item) {
                $this->validateNumeric($item);
            }
        } else {
            $this->validateNumeric($object);
        }
    }

    private function validateNumeric($object): void
    {
        if (!is_numeric($object)) {
            throw new ApiException(message: 'Значение `' . $object . '` не является допустимым int');
        }
        if ($object > Helper::MAX_SIZE_INTEGER) {
            throw new ApiException(message: $object . ' is out of range for type integer');
        }
    }

}
