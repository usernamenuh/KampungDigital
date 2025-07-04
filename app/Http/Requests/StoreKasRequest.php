<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'kades', 'rw', 'rt']);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'send_notification' => $this->boolean('send_notification'),
            'send_reminder' => $this->boolean('send_reminder'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rt_id' => 'required|exists:rts,id',
            'minggu_ke' => 'required|integer|min:1|max:53',
            'tahun' => 'required|integer|min:2020|max:2030',
            'jumlah' => 'required|numeric|min:1000',
            'tanggal_jatuh_tempo' => 'required|date|after:today',
            'keterangan' => 'nullable|string|max:1000',
            'send_notification' => 'nullable|boolean',
            'send_reminder' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'rt_id.required' => 'RT harus dipilih.',
            'rt_id.exists' => 'RT yang dipilih tidak valid.',
            'minggu_ke.required' => 'Minggu ke harus diisi.',
            'minggu_ke.min' => 'Minggu ke minimal 1.',
            'minggu_ke.max' => 'Minggu ke maksimal 53.',
            'tahun.required' => 'Tahun harus diisi.',
            'tahun.min' => 'Tahun minimal 2020.',
            'tahun.max' => 'Tahun maksimal 2030.',
            'jumlah.required' => 'Jumlah kas harus diisi.',
            'jumlah.min' => 'Jumlah kas minimal Rp 1.000.',
            'tanggal_jatuh_tempo.required' => 'Tanggal jatuh tempo harus diisi.',
            'tanggal_jatuh_tempo.after' => 'Tanggal jatuh tempo harus setelah hari ini.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.',
        ];
    }
}
