<?php

namespace App\Http\Requests\V1;

use App\Enum\Frequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
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

        if ($this->isMethod('PUT')) {
            return [
                'eventName' => 'required|string',
                'frequency' => ['required', 'exists:frequencies,name', Rule::in($allowedFrequencies)],
                'startDateTime' => 'required|date|date_format:Y-m-d H:i|before:endDateTime',
                'endDateTime' => 'nullable|date|date_format:Y-m-d H:i|after:startDateTime',
                'duration' => 'nullable|integer|between:0,480',
                'invitees' => 'nullable|array',
                'invitees.*' => 'required|exists:users,id'
            ];
        } else {
            return [
                'eventName' => 'sometimes|required|string',
                'frequency' => ['sometimes', 'required', 'exists:frequencies,name', Rule::in($allowedFrequencies)],
                'startDateTime' => 'sometimes|required|date|date_format:Y-m-d H:i|before:endDateTime',
                'endDateTime' => 'sometimes|nullable|date|date_format:Y-m-d H:i|after:startDateTime',
                'duration' => 'sometimes|nullable|integer|between:0,480',
                'invitees' => 'sometimes|nullable|array',
                'invitees.*' => 'sometimes|required|exists:users,id'
            ];
        }
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
