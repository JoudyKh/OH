<?php

namespace App\Http\Requests;

use App\Models\Info;
use App\Rules\OneOrNone;
use Illuminate\Validation\Rule;
use App\Constants\InfoValidationRules;
use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="UpdateInfoRequest",
 *     type="object",
 *     title="Update Info Request",
 *     description="Request body for updating information",
 *     @OA\Property(
 *         property="home-background_image",
 *         type="string",
 *         format="binary",
 *         description="Background image for the home, accepts PNG, JPG, JPEG",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="home-doctor_image",
 *         type="string",
 *         format="binary",
 *         description="Doctor's image for the home, accepts PNG, JPG, JPEG",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="home-doctor_cv",
 *         type="string",
 *         description="Doctor's CV file path or URL",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="home-doctor_name",
 *         type="string",
 *         description="Doctor's name",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="home-first_phrase",
 *         type="string",
 *         description="First phrase for the home page",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="home-second_phrase",
 *         type="string",
 *         description="Second phrase for the home page",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="home-third_phrase",
 *         type="string",
 *         description="Third phrase for the home page",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="social-facebook",
 *         type="string",
 *         format="url",
 *         description="Facebook URL",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="social-instgram",
 *         type="string",
 *         format="url",
 *         description="Instagram URL",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="social-youtube",
 *         type="string",
 *         format="url",
 *         description="YouTube URL",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="social-behance",
 *         type="string",
 *         format="url",
 *         description="Behance URL",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="social-tiktok",
 *         type="string",
 *         format="url",
 *         description="TikTok URL",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="social-telegram",
 *         type="string",
 *         description="Telegram handle or URL",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="social-phone_number_1",
 *         type="string",
 *         description="Primary phone number",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="social-phone_number_2",
 *         type="string",
 *         description="Secondary phone number",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="social-email",
 *         type="string",
 *         description="email",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="general-privacy_text",
 *         type="string",
 *         description="Privacy text",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="general-privacy_image",
 *         type="string",
 *         format="binary",
 *         description="Privacy image, accepts PNG, JPG, JPEG",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="general-terms_text",
 *         type="string",
 *         description="Terms text",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="general-terms_image",
 *         type="string",
 *         format="binary",
 *         description="Terms image, accepts PNG, JPG, JPEG",
 *         nullable=true
 *     )
 * )
 */
class UpdateInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request-
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request-
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'home-background_image' => ['image', 'mimes:gif,webp,png,jpg,jpeg'],
            'home-doctor_image' => ['image', 'mimes:gif,webp,png,jpg,jpeg'],
            'home-doctor_cv' => ['string'],
            'home-doctor_name' => ['string'],
            'home-first_phrase' => ['string'],
            'home-second_phrase' => ['string'],
            'home-third_phrase' => ['string'],

            'social-facebook' => ['string', 'url'],
            'social-instgram' => ['string', 'url'],
            'social-youtube' => ['string', 'url'],
            'social-behance' => ['string', 'url'],
            'social-tiktok' => ['string', 'url'],
            'social-telegram' => ['string'],
            'social-phone_number_1' => ['string'],
            'social-phone_number_2' => ['string'],
            'social-email' => ['email'],

            'general-privacy_text' => ['string'],
            'general-privacy_image' => ['image', 'mimes:gif,webp,png,jpg,jpeg'],
            'general-terms_text' => ['string'],
            'general-terms_image' => ['image', 'mimes:gif,webp,png,jpg,jpeg'],

        ];
    }
    public function messages(): array
    {
        return [
            'social-facebook.url' => 'يجب ان يكون رابط',
            'social-instgram.url' => 'يجب ان يكون رابط',
            'social-youtube.url' => 'يجب ان يكون رابط',
            'social-behance.url' => 'يجب ان يكون رابط',
            'social-tiktok.url' => 'يجب ان يكون رابط',
            'social-email.email' => 'يجب ان يكون ايميل صالح',
        ];
    }
}
