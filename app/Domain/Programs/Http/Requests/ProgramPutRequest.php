<?php
declare(strict_types=1);

namespace App\Domain\Programs\Http\Requests;

use App\Domain\Programs\Enums\DepositType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgramPutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:1'],
            'try_period_days' => ['nullable', 'int', 'min:1'],
            'deposit_type' => ['nullable', 'string', Rule::in([DepositType::FIXED, DepositType::PERCENTAGE])],
            'deposit_value' => ['nullable', 'int', 'min:0'],
            'min_tbyb_items' => ['nullable', 'int', 'min:1'],
            'max_tbyb_items' => ['nullable', 'int', 'gte:min_tbyb_items'],
            'drop_off_days' => ['nullable', 'int'],
        ];
    }
}
