<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MedicationRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
         return [
			'med_name'				=>	'required',
			'med_description'		=>	'required',
			'med_amount'			=>	'required',
        ];
    }
}
