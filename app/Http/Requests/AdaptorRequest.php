<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AdaptorRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'adaptor' => array('required' , 'regex:/[ACGT]{0,20}/' , 'max:20'),

            'default' => 'required | boolean'
        ];
    }
}