<?php

namespace Database\Seeders;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $topics = [
            [
                'name' => 'Công nghệ thông tin',
                'description' => 'Các chủ đề liên quan đến công nghệ thông tin',
                'children' => [
                    [
                        'name' => 'PHP',
                        'description' => 'Ngôn ngữ lập trình PHP',
                        'children' => [],
                    ],
                    [
                        'name' => 'Laravel',
                        'description' => 'Framework PHP Laravel',
                        'children' => [],
                    ],
                    [
                        'name' => 'MySQL',
                        'description' => 'Hệ quản trị cơ sở dữ liệu MySQL',
                        'children' => [],
                    ],
                ],
            ],
            [
                'name' => 'Ngoại ngữ',
                'description' => 'Các chủ đề về ngoại ngữ',
                'children' => [
                    [
                        'name' => 'English Grammar',
                        'description' => 'Ngữ pháp tiếng Anh',
                        'children' => [],
                    ],
                    [
                        'name' => 'TOEIC',
                        'description' => 'Bài thi TOEIC',
                        'children' => [],
                    ],
                ],
            ],
        ];

        foreach ($topics as $parentData) {
            $children = $parentData['children'];
            unset($parentData['children']);

            $parent = Topic::firstOrCreate(
                ['name' => $parentData['name']],
                array_merge($parentData, ['created_by' => $admin->id])
            );

            foreach ($children as $childData) {
                Topic::firstOrCreate(
                    ['name' => $childData['name'], 'parent_id' => $parent->id],
                    [
                        'name' => $childData['name'],
                        'description' => $childData['description'],
                        'parent_id' => $parent->id,
                        'created_by' => $admin->id,
                    ]
                );
            }
        }
    }
}
