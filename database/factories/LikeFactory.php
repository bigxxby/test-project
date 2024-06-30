<?php

namespace Database\Factories;

use App\Models\Like;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    protected $model = Like::class;

    public function definition()
    {
        return [
            'user_id' => 1, // Ваш ID пользователя
            // Дополнительные поля могут быть добавлены в зависимости от логики вашего приложения
        ];
    }
}
