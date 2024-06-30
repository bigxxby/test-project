<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'post_id' => 1, // ID поста, к которому привязан комментарий
            'user_id' => 1, // Ваш ID пользователя
            'content' => $this->faker->paragraph,
        ];
    }
}
