<?php

namespace App\Contracts;

interface OrderValidator
{
    /**
     * Valida i dati forniti.
     *
     * @param array $data
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate($data): void;
}

?>