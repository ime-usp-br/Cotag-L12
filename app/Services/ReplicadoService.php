<?php

namespace App\Services;

use App\Exceptions\ReplicadoServiceException; // Import custom exception
use Illuminate\Support\Facades\Log;
use Uspdev\Replicado\Pessoa;

/**
 * Classe de serviço para interagir com o banco de dados Replicado da USP.
 */
class ReplicadoService
{
    /**
     * Valida se o Número USP (codpes) e o e-mail fornecidos pertencem à mesma pessoa válida no Replicado.
     *
     * Este método consulta o Replicado para verificar a existência do `codpes`
     * e se o `email` fornecido está associado a esse `codpes`.
     *
     * @param  int  $codpes  O Número USP (NUSP).
     * @param  string  $email  O endereço de e-mail para validar em conjunto com o `codpes`.
     * @return bool Retorna `true` se o `codpes` e o `email` corresponderem a uma pessoa válida, `false` caso contrário.
     *
     * @throws \App\Exceptions\ReplicadoServiceException Se ocorrer um problema de comunicação com o banco de dados Replicado.
     */
    public function validarNuspEmail(int $codpes, string $email): bool
    {
        if (! str_ends_with(strtolower($email), 'usp.br')) {
            Log::warning("Replicado Validation: Attempt to validate non-USP email '{$email}' for codpes {$codpes}.");
            // Depending on strictness, this might be an early return false or even an exception.
            // For now, let it proceed to check against Replicado records.
        }

        try {
            $emailsPessoa = Pessoa::emails($codpes);

            if (empty($emailsPessoa)) {
                Log::info("Replicado Validation: No person found or no emails registered for codpes {$codpes}.");

                return false;
            }

            foreach ($emailsPessoa as $emailCadastrado) {
                if (is_string($emailCadastrado) && (strtolower(trim($emailCadastrado)) === strtolower($email))) {
                    Log::info("Replicado Validation: Success for codpes {$codpes} and email '{$email}'.");

                    return true;
                }
            }

            Log::info("Replicado Validation: Email '{$email}' does not match registered emails for codpes {$codpes}.");

            return false;

        } catch (\Exception $e) {
            Log::error("Replicado Service Error: Failed validating codpes {$codpes} and email '{$email}'. Error: ".$e->getMessage(), ['exception' => $e]);
            // Re-throw as a custom, more specific exception for better handling by callers.
            throw new ReplicadoServiceException('Replicado service communication error while validating NUSP/email.', 0, $e);
        }
    }
}
