<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(){ return auth()->check(); }

    public function rules(): array
    {
        return [
            'images.*'     => ['nullable','image','mimes:jpg,jpeg,png','max:4096'],
            'categories'   => ['required','array','min:1'],
            'categories.*'=> ['integer','exists:categories,id'],
            'condition'    => ['required','integer','between:1,5'],
            'name'         => ['required','string','max:100'],
            'brand_name'   => ['nullable','string','max:100'],
            'description'  => ['nullable','string','max:2000'],
            'price'        => ['required','integer','min:1','max:10000000'],
        ];
    }
}
