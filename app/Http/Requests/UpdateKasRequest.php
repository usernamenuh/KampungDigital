<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'kades', 'rw', 'rt']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jumlah' => 'required|numeric|min:1000',
            'status' => 'required|in:belum_bayar,lunas,terlambat',
            'minggu_ke' => 'required|integer|min:1|max:53',
            'tahun' => 'required|integer|min:2020|max:2030',
            'tanggal_jatuh_tempo' => 'required|date',
            'tanggal_bayar' => 'nullable|date',
            'metode_bayar' => 'nullable|string|in:tunai,transfer,digital,e_wallet',
            'keterangan' => 'nullable|string|max:1000',
            'bukti_bayar' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'jumlah.required' => 'Jumlah kas harus diisi.',
            'jumlah.min' => 'Jumlah kas minimal Rp 1.000.',
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status tidak valid.',
            'minggu_ke.required' => 'Minggu ke harus diisi.',
            'minggu_ke.min' => 'Minggu ke minimal 1.',
            'minggu_ke.max' => 'Minggu ke maksimal 53.',
            'tahun.required' => 'Tahun harus diisi.',
            'tahun.min' => 'Tahun minimal 2020.',
            'tahun.max' => 'Tahun maksimal 2030.',
            'tanggal_jatuh_tempo.required' => 'Tanggal jatuh tempo harus diisi.',
            'metode_bayar.in' => 'Metode pembayaran tidak valid.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.',
            'bukti_bayar.max' => 'Bukti pembayaran maksimal 1000 karakter.',
        ];
    }
}
