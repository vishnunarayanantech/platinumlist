<?php

namespace EventManagementPlatform\Validators;

interface ValidatorInterface {
    public function validate( array $data ): bool;
    public function getErrors(): array;
}
