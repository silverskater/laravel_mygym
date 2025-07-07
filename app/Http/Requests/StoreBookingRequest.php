<?php

namespace App\Http\Requests;

use App\Models\ScheduledClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure only authenticated members can make this request.
        return $this->user()?->role === 'member';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'scheduled_class_id' => 'required|exists:scheduled_classes,id',
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
            // Find the scheduled class using the validated input.
            $scheduledClass = ScheduledClass::find($this->input('scheduled_class_id'));

            // If the class wasn't found or initial validation already failed, stop here.
            if (!$scheduledClass || $validator->errors()->has('scheduled_class_id')) {
                return;
            }

            // Check if the number of members is at or over capacity.
            if ($scheduledClass->members()->count() >= $scheduledClass->capacity) {
                $validator->errors()->add(
                    'scheduled_class_id',
                    'This class is already full.'
                );
            }
        });
    }
}
