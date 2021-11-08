<?php


use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class HomeConfigSeeder extends Seeder
{
    /**
     * 运行数据库填充
     */
    public function run()
    {
        $this->call('HomeConfigSeeder');
        $this->command->info('home config seeder start!');

        $path = './home_config.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('home config seeder end!');
    }
}