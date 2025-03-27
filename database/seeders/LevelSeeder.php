<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Level 1: Baby Steps',
                'description' => "🎉 You’ve managed to complete 5 whole tasks — your to-do list officially respects you now. You're crawling through the productivity playground, wearing a cape made of sticky notes. Want to walk like a true rookie? Just finish 5 more tasks and rocket up to the next level!",
                'required_tasks' => 5,
            ],
            [
                'name' => 'Level 2: Rookie Rocket',
                'description' => "🚀 Boom! 10 tasks down and you're leaving procrastination orbit. You’re now certified to launch into mildly serious to-do missions. Word of advice: keep fuel in your productivity tank and hit 15 to evolve into the ultimate Taskinator!",
                'required_tasks' => 10,
            ],
            [
                'name' => 'Level 3: Taskinator',
                'description' => "🤖 15 tasks? You’re no longer human. You’re a task-crunching machine with a calendar for a brain. Rumors say you don't sleep—just recharge. Crush 5 more and become a wizard who bends time and to-dos!",
                'required_tasks' => 15,
            ],
            [
                'name' => 'Level 4: Workflow Wizard',
                'description' => "🪄 You’ve enchanted 20 tasks into submission with your spellbook of sticky notes. Deadlines bow before your mighty focus. Cast your final productivity spell on 5 more tasks and unlock the zen power of the Productivity Panda.",
                'required_tasks' => 20,
            ],
            [
                'name' => 'Level 5: Productivity Panda',
                'description' => "🐼 You radiate calm while completing 25 tasks with one paw tied behind your back. Your secret? Naps between tasks and bamboo snacks. Slide into 30 tasks and become a master of schedule sorcery: the Deadline Dodger!",
                'required_tasks' => 25,
            ],
            [
                'name' => 'Level 6: Deadline Dodger',
                'description' => "⏰ Deadlines run the other way when they see you coming. With 30 tasks under your belt, you now dodge meetings like a ninja and reply to emails telepathically. Clear 5 more and claim your rightful title as Checklist Champ!",
                'required_tasks' => 30,
            ],
            [
                'name' => 'Level 7: Checklist Champ',
                'description' => "🏆 You've completed 35 tasks and now wear your to-do list as a belt of honor. Checkmarks appear as if by magic. Complete 5 more and rise to fearsome power as the To-Do Tyrant. Muahaha.",
                'required_tasks' => 35,
            ],
            [
                'name' => 'Level 8: To-Do Tyrant',
                'description' => "👑 With 40 tasks annihilated, you rule your calendar with an iron stylus. People whisper of your efficiency in productivity forums. Crush 5 more to unlock your true form: the Overlord of Order.",
                'required_tasks' => 40,
            ],
            [
                'name' => 'Level 9: Overlord of Order',
                'description' => "🧠 45 tasks tamed. Chaos fears you. Post-its salute you. You’re so organized you color-code your dreams. Knock down 5 more tasks to reach the summit of taskdom and achieve legendary status.",
                'required_tasks' => 45,
            ],
            [
                'name' => 'Level 10: Legendary Task Slayer',
                'description' => "🌟 50 tasks completed?! Legend says you can complete chores just by glaring at them. You have ascended. No more levels to unlock — only respect, awe, and possibly a LinkedIn endorsement for ‘Getting Stuff Done’. Bask in your glory, Slayer.",
                'required_tasks' => 50,
            ],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }
    }
}
