<?php

namespace Azuriom\Http\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Azuriom\Rules\Slug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    use ConvertCheckbox;

    /**
     * The checkboxes attributes.
     *
     * @var array
     */
    protected $checkboxes = [
        'is_enabled',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $page = $this->route('page');

        return [
            'translations.*.locale' => ['required', Rule::in(get_available_locales_codes())],
            'translations.*.title' => ['required', 'string', 'max:150'],
            'translations.*.description' => ['required', 'string', 'max:255'],
            'translations.*.content' => ['required', 'string'],
            'slug' => ['required', 'string', 'max:100', new Slug(true), Rule::unique('pages')->ignore($page, 'slug')],
            'is_enabled' => ['filled', 'boolean'],
        ];
    }
}
