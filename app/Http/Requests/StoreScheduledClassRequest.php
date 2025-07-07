<?php

namespace App\Http\Requests;

use App\Models\ClassType;
use App\Models\ScheduledClass;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreScheduledClassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure only authenticated instructors can make this request.
        return $this->user()?->role === 'instructor';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'class_type_id' => 'required|exists:class_types,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i:s',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $classType = ClassType::find($this->input('class_type_id'));
            if (!$classType || $validator->errors()->has('class_type_id')) {
                return;
            }
            // Check if the date and time are in the future.
            if (now()->parse($this->input('date').' '.$this->input('time'))->isPast()) {
                $validator->errors()->add(
                    'time',
                    'The time is in the past.'
                );
            }
        });
    }
}
