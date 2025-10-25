<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルートで auth を掛けている前提なので true でOK
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        // エラーメッセージの :attribute を日本語に
        return ['body' => 'コメント'];
    }
}
