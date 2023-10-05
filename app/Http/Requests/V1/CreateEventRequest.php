<?php

namespace App\Http\Requests\V1;

use App\Enum\Frequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEventRequest extends FormRequest
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
        $allowedFrequencies = [
            Frequency::ONCE_OFF_NAME,
            Frequency::WEEKLY_NAME,
            Frequency::MONTHLY_NAME,
        ];

        return [
            'eventName' => 'required|string',
            'frequency' => ['required', 'exists:frequencies,name', Rule::in($allowedFrequencies)],
            'startDateTime' => 'required|date|date_format:Y-m-d H:i',
            'endDateTime' => 'nullable|date|date_format:Y-m-d H:i',
            'duration' => 'nullable|integer|between:0,60',
            'invitees' => 'nullable|array',
            'invitees.*' => 'exists:users,id'
        ];
    }

    /**
     * Prepare the correct attributes for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->eventName) {
            $this->merge(['event_name' => $this->eventName]);
        }
        if ($this->startDateTime) {
            $this->merge(['start_date_time' => $this->startDateTime]);
        }
        if ($this->endDateTime) {
            $this->merge(['end_date_time' => $this->endDateTime]);
        }
    }
}
