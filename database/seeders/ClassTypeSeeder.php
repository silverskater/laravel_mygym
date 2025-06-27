<?php

namespace Database\Seeders;


use App\Models\ClassType;
use Illuminate\Database\Seeder;

class ClassTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classTypes = [
            ['name' => 'Aerobics', 'description' => 'A rhythmic workout that combines dance and fitness.', 'duration' => 45, 'capacity' => 25, 'color' => '#FF6384'],
            ['name' => 'Barre', 'description' => 'A ballet-inspired workout that combines elements of dance and Pilates.', 'duration' => 60, 'capacity' => 15, 'color' => '#FFB1C1'],
            ['name' => 'Bootcamp', 'description' => 'An intense workout combining cardio and strength training.', 'duration' => 60, 'capacity' => 30, 'color' => '#36A2EB'],
            ['name' => 'Booty Blast', 'description' => 'A targeted workout focusing on glute strength and shape.', 'duration' => 45, 'capacity' => 15, 'color' => '#B266FF'],
            ['name' => 'Boxing', 'description' => 'A class that combines boxing techniques with cardio for a full-body workout.', 'duration' => 60, 'capacity' => 20, 'color' => '#FF9F40'],
            ['name' => 'Cardio', 'description' => 'A high-energy class focusing on cardiovascular fitness.', 'duration' => 45, 'capacity' => 30, 'color' => '#FFCD56'],
            ['name' => 'Circuit Training', 'description' => 'A combination of strength and cardio exercises in a circuit format.', 'duration' => 50, 'capacity' => 25, 'color' => '#4BC0C0'],
            ['name' => 'Core Conditioning', 'description' => 'A class that targets core muscles for stability and strength.', 'duration' => 45, 'capacity' => 15, 'color' => '#9966FF'],
            ['name' => 'CrossFit', 'description' => 'A high-intensity workout that combines various functional movements.', 'duration' => 60, 'capacity' => 20, 'color' => '#C9CBCF'],
            ['name' => 'Dance Fitness', 'description' => 'A fun and energetic class that combines dance and fitness.', 'duration' => 60, 'capacity' => 25, 'color' => '#FF6384'],
            ['name' => 'Functional Training', 'description' => 'A class that focuses on exercises that mimic everyday activities.', 'duration' => 60, 'capacity' => 25, 'color' => '#36A2EB'],
            ['name' => 'HIIT', 'description' => 'High-Intensity Interval Training for maximum calorie burn.', 'duration' => 30, 'capacity' => 25, 'color' => '#FF6384'],
            ['name' => 'Kettlebell Training', 'description' => 'A class that uses kettlebells for strength and cardio training.', 'duration' => 60, 'capacity' => 20, 'color' => '#FF9F40'],
            ['name' => 'Kickboxing', 'description' => 'A high-energy class that combines martial arts techniques with cardio.', 'duration' => 60, 'capacity' => 20, 'color' => '#FF6384'],
            ['name' => 'Pilates', 'description' => 'A class that focuses on core strength and stability.', 'duration' => 45, 'capacity' => 15, 'color' => '#B1FFB1'],
            ['name' => 'Senior Fitness', 'description' => 'A gentle class designed for older adults to improve strength and flexibility.', 'duration' => 60, 'capacity' => 20, 'color' => '#C9CBCF'],
            ['name' => 'Spin', 'description' => 'An indoor cycling class that focuses on endurance and strength.', 'duration' => 45, 'capacity' => 20, 'color' => '#36A2EB'],
            ['name' => 'Strength Training', 'description' => 'A class focused on building muscle strength and endurance.', 'duration' => 60, 'capacity' => 20, 'color' => '#4BC0C0'],
            ['name' => 'Tai Chi', 'description' => 'A gentle class that focuses on slow, controlled movements and breathing.', 'duration' => 60, 'capacity' => 20, 'color' => '#FFD966'],
            ['name' => 'TRX Suspension Training', 'description' => 'A bodyweight training class using TRX suspension straps.', 'duration' => 60, 'capacity' => 15, 'color' => '#FFD700'],
            ['name' => 'Yoga', 'description' => 'A relaxing class focusing on flexibility and mindfulness.', 'duration' => 60, 'capacity' => 20, 'color' => '#8DD3C7'],
            ['name' => 'Zumba', 'description' => 'A dance-based workout that combines Latin and international music with dance.', 'duration' => 60, 'capacity' => 30, 'color' => '#FF6384'],
        ];

        foreach ($classTypes as $classType) {
            ClassType::create($classType);
        }
    }
}
