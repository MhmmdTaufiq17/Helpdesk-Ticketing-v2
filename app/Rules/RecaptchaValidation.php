<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use ReCaptcha\ReCaptcha as GoogleReCaptcha;  // Alias untuk hindari konflik

class RecaptchaValidation implements Rule  // Ubah nama class
{
    public function passes($attribute, $value)
    {
        // Gunakan alias
        $recaptcha = new GoogleReCaptcha(config('services.recaptcha.secret_key'));
        $response = $recaptcha->verify($value, request()->ip());

        return $response->isSuccess();
    }

    public function message()
    {
        return 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.';
    }
}
