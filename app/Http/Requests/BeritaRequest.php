<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BeritaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'judul' => [
                'required',
                'string',
                'min:5',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\.,!?()]+$/u'
            ],
            'konten' => [
                'required',
                'string',
                'min:20',
                'max:50000'
            ],
            'excerpt' => [
                'nullable',
                'string',
                'max:500'
            ],
            'kategori' => [
                'required',
                'in:umum,pengumuman,kegiatan,pembangunan,kesehatan,pendidikan,ekonomi,sosial,lingkungan,keamanan'
            ],
            'tingkat_akses' => [
                'required',
                'in:rt,rw,desa'
            ],
            'status' => [
                'required',
                'in:draft,published'
            ],
            'gambar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // 5MB
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ],
            'video' => [
                'nullable',
                'file',
                'mimes:mp4,avi,mov,wmv,flv,webm',
                'max:51200' // 50MB
            ],
            'link' => [
                'nullable',
                'url',
                'max:500',
                'regex:/^https?:\/\/.+/'
            ],
            'is_pinned' => [
                'nullable',
                'boolean'
            ],
            'tags' => [
                'nullable',
                'string',
                'max:1000',
                'regex:/^[a-zA-Z0-9\s,\-]+$/'
            ]
        ];

        // Conditional validation based on tingkat_akses
        if ($this->tingkat_akses === 'rt') {
            $rules['rt_id'] = [
                'required',
                'exists:rts,id',
                'integer'
            ];
        } elseif ($this->tingkat_akses === 'rw') {
            $rules['rw_id'] = [
                'required',
                'exists:rws,id',
                'integer'
            ];
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul berita wajib diisi.',
            'judul.min' => 'Judul berita minimal 5 karakter.',
            'judul.max' => 'Judul berita maksimal 255 karakter.',
            'judul.regex' => 'Judul berita hanya boleh mengandung huruf, angka, spasi, dan tanda baca dasar.',
            
            'konten.required' => 'Konten berita wajib diisi.',
            'konten.min' => 'Konten berita minimal 20 karakter.',
            'konten.max' => 'Konten berita maksimal 50.000 karakter.',
            
            'excerpt.max' => 'Ringkasan maksimal 500 karakter.',
            
            'kategori.required' => 'Kategori berita wajib dipilih.',
            'kategori.in' => 'Kategori berita tidak valid.',
            
            'tingkat_akses.required' => 'Tingkat akses wajib dipilih.',
            'tingkat_akses.in' => 'Tingkat akses tidak valid.',
            
            'status.required' => 'Status berita wajib dipilih.',
            'status.in' => 'Status berita tidak valid.',
            
            'rt_id.required' => 'RT wajib dipilih untuk berita tingkat RT.',
            'rt_id.exists' => 'RT yang dipilih tidak valid atau tidak ditemukan.',
            'rt_id.integer' => 'ID RT harus berupa angka.',
            
            'rw_id.required' => 'RW wajib dipilih untuk berita tingkat RW.',
            'rw_id.exists' => 'RW yang dipilih tidak valid atau tidak ditemukan.',
            'rw_id.integer' => 'ID RW harus berupa angka.',
            
            'gambar.image' => 'File yang diupload harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus: JPEG, PNG, JPG, GIF, atau WebP.',
            'gambar.max' => 'Ukuran gambar maksimal 5MB.',
            'gambar.dimensions' => 'Dimensi gambar minimal 100x100px dan maksimal 4000x4000px.',
            
            'video.file' => 'File yang diupload harus berupa file video.',
            'video.mimes' => 'Format video harus: MP4, AVI, MOV, WMV, FLV, atau WebM.',
            'video.max' => 'Ukuran video maksimal 50MB.',
            
            'link.url' => 'Link harus berupa URL yang valid.',
            'link.max' => 'Link maksimal 500 karakter.',
            'link.regex' => 'Link harus dimulai dengan http:// atau https://',
            
            'is_pinned.boolean' => 'Nilai pin berita tidak valid.',
            
            'tags.max' => 'Tags maksimal 1000 karakter.',
            'tags.regex' => 'Tags hanya boleh mengandung huruf, angka, spasi, koma, dan tanda hubung.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul berita',
            'konten' => 'konten berita',
            'excerpt' => 'ringkasan',
            'kategori' => 'kategori',
            'tingkat_akses' => 'tingkat akses',
            'status' => 'status',
            'rt_id' => 'RT',
            'rw_id' => 'RW',
            'gambar' => 'gambar',
            'video' => 'video',
            'link' => 'link eksternal',
            'is_pinned' => 'pin berita',
            'tags' => 'tags',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
