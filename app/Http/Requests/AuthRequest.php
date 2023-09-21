<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class AuthRequest extends FormRequest
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
        switch ($this->getRequestUri()) {
            case '/api/registration':
                $emailRule = 'required|email|unique:users,email';
                break;
            default:
                $emailRule = 'required|email';
        }

        return [
            'email' => $emailRule,
            'password' => 'required|string'
        ];
    }
}
