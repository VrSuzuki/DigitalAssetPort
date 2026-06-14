<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContentRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'genre_id' => ['required', 'exists:genres,id'],
            'sub_genre_id' => [
                'required',
                Rule::exists('sub_genres', 'id')->where('genre_id', $this->input('genre_id')),
            ],
            'format' => ['required', 'string', 'max:40'],
            'description' => ['required', 'string', 'max:5000'],
            'tags' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0', 'max:100000'],
            'license_type' => ['nullable', 'string', 'max:80'],
            'environment' => ['nullable', 'string', 'max:120'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'delete_image_ids' => ['nullable', 'array'],
            'delete_image_ids.*' => ['integer'],
            'content_file' => ['nullable', 'file', 'max:102400'],
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'コンテンツ名',
            'genre_id' => 'ジャンル',
            'sub_genre_id' => 'サブジャンル',
            'format' => '形式',
            'description' => 'コンテンツ紹介文',
            'price' => '価格',
            'thumbnail' => 'コンテンツ画像',
            'images' => 'コンテンツ画像',
            'images.*' => 'コンテンツ画像',
            'content_file' => 'コンテンツファイル',
        ];
    }
}
